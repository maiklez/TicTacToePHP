<h1>
<p align="center">
Tic Tac Toe
</p>
</h1>
## Source code
The source code can be found in the HomeController

The objective of this test it is to build a functional tic-tac-toe. It is a traditional game where players have to place three marks of the same color in a row.

Technology Laravel.

POST web service listening to requests on /api/tic-tac-toe/play that matches following specification

## Request body

Public web service should accept body param with a structure similar to this one:

```
{
  "matchId": "5a5c6f9b4d749d008e07e695", //string, identifies the current match, required
  "boardState": [
    "x", "-", "-", // first row of the game board, positions 0, 1, 2
    "-", "o", "o", // second row of the game board, positions 3, 4, 5
    "-", "-", "x" // third row of the game board, positions 6, 7, 8
  ], // array of chars ( one of ['o','x','-']), required
  "nextMove": {
    "char": "o", // char one of ['o','x'], required
    "position": 4 // number from 0 to 8, required 
  }, // object, represents the next move of the player, required only on input
  "history": [
    {
      "char": "x",
      "position": 0
    }
  ], // array of move objects, optional
  "key": "eyJpdiI6IjR6QURjWHJlWHdCeWs1YldlVXZLWlE9PSIsInZhbHVlIjoiSmZpT0p2U3pWOEZKV21NYVJPaDV3aytFQVJucGFxZ3RIdWNIMnFsRkY0YnlTb0o1dmltN2RsVXR2bkNkTFVGQ1E3VEJkbVhzQTc4Q09KRDJjSzNLcTllemlxNk1OU1c5QTVBZnRpMkNnNlh2eUU2OWpManFvU0I5ZDJhdWNENU5mTVpZQUVEVVlmOXdkNEQrQ0Nnd0ZFOTdxMUw2TEFXb2FFbUtDbGJVWHRwdGg4UTkwXC83eHI5Sm9QWVlmbWNuVFVvSDlycWhQaCtEM0RibmYrT050dm5rd2dubkhmRG5oNElJOFNEUCtQNmQ5SHdGMzFwNllDSlNvdmtcL3dEandpIiwibWFjIjoiYzhiOTdlOWNhMzI0Y2YwNjIwYWI2ZjE0MWE0OTZlY2VlNmY1YTU2N2YwYzcyMDk5ZTUzMThiYjkzOGUzM2EwNCJ9", // string
  "winner": "o", // char with the winner player
}
```

This is the human specification of the previous payload:

* `matchId`: Mandatory string that identifies the current match.
* `boardState`: Array of strings that contain the state of the board. It has a size of 3 x 3 row and each element has one of the following values: `x`, `-` or `o`
* `nextMove`: Mandatory object that contains information of the next move. It contains two mandatory fields that indicates the type of mark you want to add (valid values: `x` and `o`) and another one that indicates the position where you want to put it.
* `history`: Mandatory list of previous moves sorted by time (ascendent). The structure of the items it contains is the same than `nextMove` field.
* `key`: Mandatory string that has to be passed bettwen calls to certifie the math, board and history.
* `winner`: optional. This is for the response that indicates when it was a winner.


## Algorithm for machine moves
http://www.cs.jhu.edu/~jorgev/cs106/ttt.pdf
https://es.wikihow.com/ganar-jugando-tres-en-raya

We have two diferent strategies. We are going to make diferences if I start the game or if the other player start the game.

The movements are based on that study of the tic tac toe: http://www.cs.jhu.edu/~jorgev/cs106/ttt.pdf

If I start the most probabilistic chance to win is starting from the corner. On the other side, if the opponent start, if he start in the corner I will put on the center, otherwise in a corner.

* Si empiezo yo: 
        
		Mov 1 - esquina - 0

  		Mov 2 - si el otro en el centro -> esquina opuesta a la anterior - 8
                si no -> en cualquier otra esquina, con un espacio vacío entre ambas - 2,6  ---> el otro en [1,2,5,8] --> yo 6
                                                                                                 el otro en [3,6,7]   --> yo 2
       		Mov 3 - Puedo ganar? si --> gana y Fin partida
				Puedo perder? si --> no pierdas!
                              no --> jugó centro? si --> a la otra esquina  (la que queda)
                                                  no --> a la otra esquina con dos filas ganadoras (quedan dos esquinas)


* Si empieza el otro:
        
		Mov 1
        - Fue al centro? -> yo a la esquina (0)
        - Fue a una esquina? -> yo al centro - (4)
        - Fue lateral -> yo al centro - (4)

   		Mov 2
        - Puedo perder? si --> no pierdas!                            
						no --> (Estoy en el centro) 
                               El está en esquinas  --> a un borde (1, 3, 5, 7) --> no perder
                               El está en laterales --> a una esquina (0,2,6,8) --> ganar
                               El está en esquina y lateral --> cualquier sitio vacio --> no perder

        
 	  	Mov 3
        - Puedo ganar? si --> gana
        - Puedo perder? si --> no pierdas!
                        no --> A la fila ganadora libre


		Cualquier Mov 4
        - Puedo ganar? si --> gana
        - Puedo perder? si --> no pierdas! 
	

## Relevant methods.

play
* API call - Request -> Validate Entry -> Move -> if not finish - BotMove -> Response

getResponse
* Makes the response to the API. matchId, boardState, history (optional), key, winner (optional)

move
* Move a char in the board

getTheOtherChar
* Finds the opposite char (player)  - Return 'o' or 'x'

getMyBestMoveIStart
* Finds the best move for the char in that number of movements if this char start the game. Return a move['char'] and move['position']

getFreeCell
* Finds an empty cell.  - Return cell position or false

getFreeBorder
* Finds an empty cell in the border.  - Return cell position or false

getFreeCorner
* Finds an empty cell in the corner.  - Return cell position or false

getMyBestMoveYouStart
* Finds the best move for the char in that number of movements if the other char start the game. Return a move['char'] and move['position']

isInBorder
* Finds if there are a char in the border.  - Return true or false

isInCorner
* Finds if there are a char in a corner.  - Return true or false

canFinish
* Finds if the player char can win in the next turn. Return false or the cell position where you can win.

getWinningRow
* Finds if there are some chance to win for the char player. If there are a row with two equal chars and an empty cell. Return the row key of WINNING_ROWS or false.

logicValidations
* Validate the input movement in the board.  - Return true or error

isAWinner  - Return winner char or false
* Finds if the board has a winner

isBoardComplete  - Return true or false
* Finds if the board is full. No empty cells

isFirstMovement  - Return true or false
* Finds if it is the first movement. The board is empty.

inputValidations
* Validate mandatory fields

historyValidations
* Validate de history input

getNewGame
*Generate a new game

getError
* Generate an error to return in a json response

uniqidReal
* Generate a unique id string

getRandomBool
* Generate a randon boolean - Return true or false


## Errors detected

	const INVALID_PAYLOAD = "Not valid payload format";
	const INVALID_BOARD_STATE = "You can't change the board state. That's cheating.";
	const INVALID_MATCH_ID = "Not valid matchId";
	const INVALID_HISTORY = "You can't change the history. And never can't.";
	const INVALID_KEY_ID = "Invalid key ID";
	const INVALID_MOVE = "Not valid move";
	const MATCH_FINISH = "Match has finished";


## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
