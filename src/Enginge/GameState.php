<?php
namespace Engine;
enum GameState
{
    case Preparing;
    case WaitingForMove;
    case Resolving;
    case GameOver;
    case Exited;
}
