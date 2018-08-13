<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use Maiklez\MaikBlog\Models\Post;
use Maiklez\MaikBlog\Models\Category;
use Maiklez\MaikBlog\Models\Tag;

use Mail;
use Auth;
use App\Models\Preguntas\Preguntas;
use App\Models\Ventas\Producto;
use App\Models\Ventas\Suscripcion;
use App\Models\Preguntas\Materia;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Auth\Events\Registered;
use App\User;
use App\Http\Controllers\Admin\User\PagosController;
use Carbon\Carbon;

/** All Paypal Details class **/
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

use App\Models\PDF;

class HomeController extends Controller
{
	
	const INVALID_PAYLOAD = "Not valid payload format";
	
	const INVALID_BOARD_STATE = "You can't change the board state. That's cheating.";
	const INVALID_MATCH_ID = "Not valid matchId";
	const INVALID_HISTORY = "You can't change the history. And never can't.";
	
	const INVALID_KEY_ID = "Invalid key ID";
	
	const INVALID_MOVE = "Not valid move";
	const MATCH_FINISH = "Match has finished";
	
	const WINNING_ROWS = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
	const CORNERS = [0,2,6,8];
	const BORDER = [1,3,5,7];
	const CENTER = 4;
	
	const FREE_CELL = "-";
	const NEW_BOARD = ["-", "-", "-", "-", "-", "-", "-", "-", "-"  ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		    	
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return view('home');
    }
    
    /**
     * play to TicTacToe
     *
     * @param  Request  $request
     * @return Response
     */
    public function play(Request $request)
    {
    	$payload = json_decode(request()->getContent(), true);
    	    	
    	//bad json
    	if(is_null($payload)){
    		return response()->json( $this->getError(HomeController::INVALID_PAYLOAD) );
    	}
    	
    	if(empty($payload)){
    		//new game
    		return response()->json($this->getNewGame());
    	}    	

    	$validations = $this->inputValidations($request);
    	if($validations!==true){
    		return response()->json( $this->getError($validations) );
    	}    		

    	$matchId=$request->matchId;
    	$boardState = $request->boardState;
    	
    	$next_move=$request->nextMove;
    	$history = is_null($request->history)?[]:$request->history;
    	
    	$validations = $this->logicValidations($boardState, $next_move, $history);
    	if($validations!==true){
    		return response()->json( $this->getError($validations) );
    	}
    	    	
    	//Move
    	$this->move($boardState, $history, $next_move);    	
    	    	
    	if($this->isBoardComplete($boardState) ){
    		return response()->json( $this->getError(HomeController::MATCH_FINISH) );
    	}
    	$winner =  $this->isAWinner($boardState);
    	if($winner){
    		
    	}
    	
    	$mychar = $this->getTheOtherChar($next_move['char']);    	
    	$start = (count($history)%2==0)?true:false;
    	
    	if($start){
    		$n_movement = count($history)/2 + 1;    		
    		$my_best_move = $this->getMyBestMoveIStart($boardState, $n_movement, $mychar);
    	}else{
    		$n_movement = (count($history)+1)/2;
    		$my_best_move = $this->getMyBestMoveYouStart($boardState, $n_movement, $mychar);
    	}
    	
    	$this->move($boardState, $history, $my_best_move);
    	if($this->isBoardComplete($boardState)){
    		return response()->json( $this->getError(HomeController::MATCH_FINISH) );
    	}
    	$winner = $this->isAWinner($boardState);
    	//fin
    	    	    	
    	$salida['matchId'] = $matchId;
    	$salida['boardState'] = $boardState;    	
    	$salida['history']=$history;
    	$cript = $salida;
    	$salida['key'] =  encrypt($cript);
    	
    	return response()->json($this->getResponse($matchId, $boardState, $history, $winner));    	
    }
	private function getResponse($matchId, $boardState, $history=null, $winner=false){
		$salida['matchId'] = $matchId;
		$salida['boardState'] = $boardState;
		if(!is_null($history)) $salida['history']=$history;
		$cript = $salida;
		$salida['key'] =  encrypt($cript);
		
		if($winner) $salida['winner']=$winner;
		
		return $salida;
	}
    private function move(&$board, &$history, $move){
    	array_push($history, $move);
    	$board[$move['position']]=$move['char'];
    }
    
    private function getTheOtherChar($char){
    	$otherchar = $char==='x'?'o':'x';
    	return $otherchar;
    }
    
    private function getMyBestMoveIStart($board, $n_movement, $char){

    	$bestMove['char']=$char;
    	
    	//I can win?
    	$canWin = $this->canFinish($board, $char);
    	if($canWin){
    		$bestMove['position']=$canWin;
    		return $bestMove;
    	}
    	//I can loose?
    	$canLoose = $this->canFinish($board, $this->getTheOtherChar($char));
    	if($canLoose){
    		$bestMove['position']=$canLoose;
    		return $bestMove;
    	}
    	
    	//I start so $n_movement > 1
    	switch ($n_movement){
    		case 2:	
    			if($board[HomeController::CENTER]===$this->getTheOtherChar($char)){
    				$bestMove['position']=8;
    			}else{
    				if($board[1]===$this->getTheOtherChar($char)
    						||$board[2]===$this->getTheOtherChar($char)
    						||$board[5]===$this->getTheOtherChar($char)
    						||$board[8]===$this->getTheOtherChar($char))
    				{
    					$bestMove['position']=6;
    				}else{
    					$bestMove['position']=2;
    				}
    			}
    			break;
    			
    		case 3:
    			if($board[HomeController::CENTER]===$this->getTheOtherChar($char)){
    				$bestMove['position']= $this->getFreeCorner($board);
    			}else{
    				if($board[8]===HomeController::FREE_CELL){
    					$bestMove['position']=8;
    				}else{
    					if($board[2]===$char){
    						$bestMove['position']=6;
    					}else{
    						$bestMove['position']=2;
    					}
    				}
    			}
    			break;
    		default:$bestMove['position']=$this->getFreeCell($board);
    	}    	
    	
    	return $bestMove;
    }
    
    private function getFreeCell($board){
    	foreach ($board as $key=>$cell){
    		if($cell===HomeController::FREE_CELL) return $key;
    	}
    	return false;
    }
    
    private function getFreeBorder($board){
    	foreach (HomeController::BORDER as $border){
    		if($board[$border]===HomeController::FREE_CELL) return $border;
    	}
    	return false;
    }
    
    private function getFreeCorner($board){
    	foreach (HomeController::CORNERS as $corner){
    		if($board[$corner]===HomeController::FREE_CELL) return $corner;
    	}
    	return false;
    }
    
    private function getMyBestMoveYouStart($board, $n_movement, $char){
    	 
    	$bestMove['char']=$char;
    	
    	//I can win?
    	$canWin = $this->canFinish($board, $char);
    	if($canWin){
    		$bestMove['position']=$canWin;
    		return $bestMove;
    	}
    	//I can loose?
    	$canLoose = $this->canFinish($board, $this->getTheOtherChar($char));
    	if($canLoose){
    		$bestMove['position']=$canLoose;
    		return $bestMove;
    	}
    	
    	switch ($n_movement){
    		case 1:
    			if($board[HomeController::CENTER] === $this->getTheOtherChar($char)){
    				$bestMove['position']=0;
    			}else{
    				$bestMove['position']=HomeController::CENTER;
    			}
    			break;
    		//I'm in center, or I play to no loose
    		case 2:
    			$isInCorner = $this->isInCorner($board, $this->getTheOtherChar($char));    			
    			$isInBorder = $this->isInBorder($board, $this->getTheOtherChar($char));
    			
    			if($isInCorner && $isInBorder){
    				$bestMove['position']=$this->getFreeCell($board);
    			}else if ($isInCorner){
    				$bestMove['position']=$this->getFreeBorder($board);
    			}else{
    				$bestMove['position']=$this->getFreeCorner($board);
    			}    			
    			break;
    		
    		
    		default:
    			$bestMove['position']=$this->getFreeCell($board);
    			break;
    	}
    	 
    	return $bestMove;
    }
    
    private function isInBorder($board, $char){
    	foreach (HomeController::BORDER as $border){
    		if($board[$border]===$char) return true;
    	}
    	return false;
    }
    private function isInCorner($board, $char){
    	foreach (HomeController::CORNERS as $corner){
    		if($board[$corner]===$char) return true;
    	}
    	return false;
    }
    
    //false or the position number
    private function canFinish($board, $char){    	
    	$winRow = $this->getWinningRow($board, $char);
    	if($winRow){
    		$row = HomeController::WINNING_ROWS;
    		foreach ($row[$winRow] as $cell){
    			if($board[$cell]===HomeController::FREE_CELL) return $cell;
    		}
    	}
    	return false;
    }
    
    private function getWinningRow($board, $char){    	    	
    	foreach (HomeController::WINNING_ROWS as $keyrow => $winrow){
    		$n_chars=0;
    	    $n_empty=0;
    		foreach ($winrow as $cell){
    			if($board[$cell]===$char)$n_chars++;
    			elseif($board[$cell]===HomeController::FREE_CELL) $n_empty++;
    		}
    	
    		if ($n_chars==2&&$n_empty>0) return $keyrow;
    	}
    	return false;
    }
    
    private function logicValidations($board, $nextMove, $history){
    	//- is finish match?
    	//if there are winner or boardState is complete
    	if($this->isBoardComplete($board) || $this->isAWinner($board)){
    		return HomeController::MATCH_FINISH;
    	}
    	//- is valid movement?
    	//if nextMove.char is correct and  empty cell in boardState[nextMove.position] --> valid    	
    	if(count($history)>0 ){
    		$lastMove=$history[count($history)-1];
    		if($nextMove['char']!==$this->getTheOtherChar($lastMove['char']) ){
    			return HomeController::INVALID_MOVE;
    		}
    	}
    	if($nextMove['position'] < 0 || $nextMove['position'] > 8){
    		return HomeController::INVALID_MOVE;
    	}
    	if($board[$nextMove['position']]!=='-' ){
    		return HomeController::INVALID_MOVE;
    	}
    	    	
    	return true;
    }
    
    private function isAWinner($board){
    	foreach (HomeController::WINNING_ROWS as $rowkey => $row){
    		if($board[$row[0]]===$board[$row[1]] 
    				&& $board[$row[1]]===$board[$row[2]]  
    				&& $board[$row[1]]!==HomeController::FREE_CELL)
    		{
    			return 	$board[$row[1]];
    		}
    	}
    	return false;
    }
    
    private function isBoardComplete($board){
    	foreach ($board as $cell){
    		if($cell==='-'){
    			return false;
    		}
    	}    	 
    	return true;
    }
    
    private function isFirstMovement($board){    	
    	foreach ($board as $cell){
    		if($cell==='x' || $cell==='o'){
    			return false;
    		}
    	}    	
    	return true;
    }
    
    private function inputValidations($request){
    	
    	$validator = validator($request->all(), [
    			'matchId' => 'required|string',
    			'boardState' => 'required|array|size:9',
    			'key' => 'required|string',
    			'nextMove' => 'required',
    			'nextMove.char' => 'required|size:1',
    			'nextMove.position' => 'required|integer',
    	]);
    	
    	if ($validator->fails()) {
    		return HomeController::INVALID_PAYLOAD;
    	}
    	//validate keys
    	$matchId=$request->matchId;
		
    	try{
    		$keyDecoded = decrypt($request->key);
    	} catch (\DecryptException $e) {
    		return response()->json( $this->getError(HomeController::INVALID_KEY_ID));
    	}
    	
    	if($matchId!==$keyDecoded['matchId']){
    		return HomeController::INVALID_MATCH_ID;
    	}
    	
    	$boardState=$request->boardState;
    	 
    	if($boardState!==$keyDecoded['boardState']){
    		return HomeController::INVALID_BOARD_STATE;
    	}
    	
    	if(!$this->isFirstMovement($request->boardState)){
    		$nextValidations = $this->historyValidations($request, $keyDecoded);
    		if($nextValidations!==true){
    			return $nextValidations;
    		}
    	}
    	
    	return true;
    }
    
    private function historyValidations($request, $keyDecoded){
    	$second_validator = validator($request->all(), [    			
    			'history' => 'required|array',    			
    	]);
    	
    	if ($second_validator->fails()) {
    		return HomeController::INVALID_PAYLOAD;
    	}
    	//validate history    	
    	if($request->history!==$keyDecoded['history']){
    		return HomeController::INVALID_HISTORY;
    	}
    	return true;
    }
    
    //"nextMove":{"char":"x","position":8}
    //remove last move from history
    
    private function getNewGame(){
    	$matchId=time()  . $this->uniqidReal();    	
    	$boardState = HomeController::NEW_BOARD;
    	$history=null;
    	
    	//who start?
    	if((bool)random_int(0, 1)){
    		//I start
    		if((bool)random_int(0, 1)){
    			$char = 'o';
    		}else{
    			$char = 'x';
    		}
    		
    		$position=0;
    		$nextMove['char']=$char;
    		$nextMove['position']=$position;
    		$history=[];
    		$this->move($boardState, $history, $nextMove);    			
    		    		
    	}
    	return $this->getResponse($matchId, $boardState, $history);
    }
    
    private function getError($message){
    	$error['error'] = true;
    	$error['message'] = $message;
    	return $error;
    }
    
    private function uniqidReal($lenght = 14) {
    	// uniqid gives 13 chars, but you could adjust it to your needs.
    	if (function_exists("random_bytes")) {
    		$bytes = random_bytes(ceil($lenght / 2));
    	} elseif (function_exists("openssl_random_pseudo_bytes")) {
    		$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    	} else {
    		throw new \Exception("no cryptographically secure random function available");
    	}
    	return substr(bin2hex($bytes), 0, $lenght);
    }
    
}
