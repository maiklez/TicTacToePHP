<h1>
<p align="center">
Tic Tac Toe
</p>
</h1>
## Source code
The source code can be found in the HomeController


## Response
Example JSON Response
{
  "matchId": "5a5c6f9b4d749d008e07e695",
  "boardState": [
    "x", "-", "-", 
    "0", "o", "o", 
    "-", "-", "x" 
  ], 
   
  "history": [
    {
      "char": "x",
      "position": 0
    }
  ],
  "key": "eyJpdiI6Im5vSEhaanZTWmxMZXVNOWlvcVhHYWc9PSIsInZhbHVlIjoibVRDM2N5UWtYcnBMVlduQUl1Zmt6Q0FabmlOU2c4M3paTENTSWtISzZsd3R4MGQ3ZWlNRjM0TVhYTnY0aXE3OXFGQ1MrRm45RTQzdVpzejZteDhTNGhzUUNod1wvRFJpTEtDckszeFl0dGRyZ1pNSW9QakxxcjNZTjhNWUlkTVJNMzNKRFRNRFJtNDhSMXV2RkRZWE5LRGE0SFpiUEl6RWowUlpNd2dOWGxUUkF0cmp3Y05mSFZMV2pDS0w3QjgrT2hXWUNMOGpRSEE2VlwvbmFkU3hhandXbXZ0cFVROXZDeG9Fc1E0RHM5RlFNWE96TVRLRituUmttVXBBRndRNWZmYzQySHZuQ3NpSDZFYVJBTFI0eU9waDRHRFBtTFVJeWZxa29LRUZ4M2tKV3pFdWE3bGR5ZlM4T0I1ZktlWU1oMW51U09tRDZPdkd4ZU9HR3FHYkJqNGM0VXRkU1YyQ3BCb1Vhb0VjZUVwZVBCQTZrZGJGdGZHRkcydmFkRjJFcFJNYlVWZVRcL0E3RGtBU3RWS2tQRXNxMk5lUWRCZjE1ZjJGOFwvRHFhcnhySUNXRzBmRjJPanJWY3NoaVpxeTA2SjV5Zm5Ubmd3aW9GVEdad1Y5NTdhRUNncnk0OXM1dUhWdzF2ZmdrWGx0bjJveVlvMWFHV1YxMUkzd0tPQVkyYVY5YWJ5ZXkzTjE2N2o3VFNMVUh6ZzVhUm9qaUFlaGFoOUFvOXhjWW1BQ1RiOTNEckxFcERSTGx1XC8xY2hNYjI1VG14dklTMFpuNWxRdDdZdEkzbUFnOFNROUxyVmNLdEdadUt5OUFqbUlZY0ZVZkt4ZUhVcUh1WGJ0azJNYmZVUXlxM1gwYlwvOFJ2NCt4MUhjdGVzdGxoenc9PSIsIm1hYyI6IjhhMmIzNTUxNDMyMjI1ZjQxMzQyYWRlMWEwZTMyNGE1ZWNjMWFkOTIzZDEyM2IzM2Y4MTdmNjU1N2UxN2I0YzUifQ==",
  "winner": "o",
}


## Request
{} to start new game.

Example JSON Request
{
  "matchId": "5a5c6f9b4d749d008e07e695",
  "boardState": [
    "x", "-", "-", 
    "0", "o", "o", 
    "-", "-", "x" 
  ], 
  "nextMove": {
    "char": "o", 
    "position": 4 
  }, 
  "history": [
    {
      "char": "x",
      "position": 0
    }
  ],
  "key": "eyJpdiI6Im5vSEhaanZTWmxMZXVNOWlvcVhHYWc9PSIsInZhbHVlIjoibVRDM2N5UWtYcnBMVlduQUl1Zmt6Q0FabmlOU2c4M3paTENTSWtISzZsd3R4MGQ3ZWlNRjM0TVhYTnY0aXE3OXFGQ1MrRm45RTQzdVpzejZteDhTNGhzUUNod1wvRFJpTEtDckszeFl0dGRyZ1pNSW9QakxxcjNZTjhNWUlkTVJNMzNKRFRNRFJtNDhSMXV2RkRZWE5LRGE0SFpiUEl6RWowUlpNd2dOWGxUUkF0cmp3Y05mSFZMV2pDS0w3QjgrT2hXWUNMOGpRSEE2VlwvbmFkU3hhandXbXZ0cFVROXZDeG9Fc1E0RHM5RlFNWE96TVRLRituUmttVXBBRndRNWZmYzQySHZuQ3NpSDZFYVJBTFI0eU9waDRHRFBtTFVJeWZxa29LRUZ4M2tKV3pFdWE3bGR5ZlM4T0I1ZktlWU1oMW51U09tRDZPdkd4ZU9HR3FHYkJqNGM0VXRkU1YyQ3BCb1Vhb0VjZUVwZVBCQTZrZGJGdGZHRkcydmFkRjJFcFJNYlVWZVRcL0E3RGtBU3RWS2tQRXNxMk5lUWRCZjE1ZjJGOFwvRHFhcnhySUNXRzBmRjJPanJWY3NoaVpxeTA2SjV5Zm5Ubmd3aW9GVEdad1Y5NTdhRUNncnk0OXM1dUhWdzF2ZmdrWGx0bjJveVlvMWFHV1YxMUkzd0tPQVkyYVY5YWJ5ZXkzTjE2N2o3VFNMVUh6ZzVhUm9qaUFlaGFoOUFvOXhjWW1BQ1RiOTNEckxFcERSTGx1XC8xY2hNYjI1VG14dklTMFpuNWxRdDdZdEkzbUFnOFNROUxyVmNLdEdadUt5OUFqbUlZY0ZVZkt4ZUhVcUh1WGJ0azJNYmZVUXlxM1gwYlwvOFJ2NCt4MUhjdGVzdGxoenc9PSIsIm1hYyI6IjhhMmIzNTUxNDMyMjI1ZjQxMzQyYWRlMWEwZTMyNGE1ZWNjMWFkOTIzZDEyM2IzM2Y4MTdmNjU1N2UxN2I0YzUifQ==",
}


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
