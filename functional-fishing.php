<?php
session_start();
/**********************************************
 * STARTER CODE
 **********************************************/

/**
 * clearSession
 * This function will clear the session.
 */
function clearSession()
{
  session_unset();
  header("Location: " . $_SERVER['PHP_SELF']);
}

/**
 * Invokes the clearSession() function.
 * This should be used if your session becomes wonky
 */
if (isset($_GET['clear'])) {
  clearSession();
}

/**
 * getResponse
 * Gets the response history array from the session and converts to a string
 * 
 * This function should be used to get the full response array as a string
 * 
 * @return string
 */
function getResponse()
{
  return implode('<br><br>', $_SESSION['functional_fishing']['response']);
}

/**
 * updateResponse
 * Adds a new response to the response array found in session
 * Returns the full response array as a string
 * 
 * This function should be used each time an action returns a response
 * 
 * @param [string] $response
 * @return string
 */
function updateResponse($response)
{
  if (!isset($_SESSION['functional_fishing'])) {
    createGameData();
  }

  array_push($_SESSION['functional_fishing']['response'], $response);

  return getResponse();
}

/**
 * help
 * Returns a formatted string of game instructions
 * 
 * @return string
 */
function help()
{
  return 'Welcome to Functional Fishing, the text based fishing game. Use the following commands to play the game: <span class="red">eat</span>, <span class="red">fish</span>, <span class="red">fire</span>, <span class="red">wood</span>, <span class="red">bait</span>. To restart the game use the <span class="red">restart</span> command For these instruction again use the <span class="red">help</span> command';
}

/**********************************************
 * YOUR CODE BELOW
 **********************************************/

/**
 * createGameData
 * Create a game data array: to hold the data that will be tracked and keep them remembered from each page refresh
 */
function createGameData() {
  // Creste a new session variable
  $_SESSION['functional_fishing'] = [
    // response, fish, wood, bait, fire are keys inside the fishing array
    'response' => [],
    'fish' => 2,
    'wood' => 2,
    'bait' => 3,
    'fire' => false// means thre is no fire at the very begining
  ];

  // you should always return something in a function
  // check whether the session has been set by a boolean
  return isset($_SESSION['functional_fishing']);
}


/**
 * fire
 *
 */
function fire() {
  // other than using $_POST() here, we use $_SESSION() because we are going to retrieve all the datas that were saved on the web server before
  if ($_SESSION['functional_fishing']['fire']) {// if 'fire' has been submitted before, then there is a data 'fire' in the session) 
    // set fire to false by using =
    $_SESSION['functional_fishing']['fire'] = false;// we will put out that fire started before, after doing that there will be no fire therefore we will set $_SESSION['camping']['fire'] to false.
    return "You have put out the fire";
  } else if ($_SESSION['functional_fishing']['wood'] > 0) {
    $_SESSION['functional_fishing']['fire'] = true;
    $_SESSION['functional_fishing']['wood'] --;
    return "You have started the fire.";
  } else {
    return "There is no more wood.";
  }
}

/**
 * bait
 * Will increase the amount by 1, if the fire is false
 */
function bait() {
  if (!$_SESSION['functional_fishing']['fire']) {
    $_SESSION['functional_fishing']['bait'] ++;
    return "You have found a bait.";
  } else {
    return "You must put out the fire.";
  }
}

/**
 * wood
 * Will increase the amount by 1, if the fire is false
 */
function wood() {
  if (!$_SESSION['functional_fishing']['fire']) {// or check whetherthe fire is true by using ===: '$_SESSION['functional_fishing']['fire'] === false'
    $_SESSION['functional_fishing']['wood'] ++;
    return "You have found a piece of wood.";
  } else {
    return "You must put out the fire.";
  }
}

/**
 * fish
 * if the fire is true
 *  the player need to put out the fire first
 * if the fire is false
 *  if the player have at least 1 piece of bait
 *    will go fishing
 *    bait--
 *    fish++
 */
function fish() {
  if ($_SESSION['functional_fishing']['fire']) {
    return "You must put out the fire.";
  } else if ($_SESSION['functional_fishing']['bait'] > 0) {
    $_SESSION['functional_fishing']['bait'] --;
    /**
     * rand(0, 1), 0 means false, 1 means true
     */
    $rand = rand(0, 1);
    if ($rand) {
      // if 1 is selected
      $_SESSION['functional_fishing']['fish'] ++;
      return "You have caught a fish.";
    } else {
      // if 0 is selected
      return "Oops you didn't catch a fish.";
    }
  } else {
    return "You do not have enough bait."; 
  }
  
}

/**
 * eat
 * if the fire is false
 *  the player need to atart the fire
 * if the fire is true
 *    if the player have at least 1 fish
 *      will go eating
 *      fish--
 */
function eat() {
  if (!$_SESSION['functional_fishing']['fire']) {
    return "You must start the fire.";
  } else if ($_SESSION['functional_fishing']['fish'] > 0) {
    $_SESSION['functional_fishing']['fish'] --;
    return "You have eaten a fish.";
  }
}

/**
 * inventory
 */
function inventory() {
  $inventory = 
  "{$_SESSION['functional_fishing']['fish']} fish;
  {$_SESSION['functional_fishing']['wood']} wood;
  {$_SESSION['functional_fishing']['bait']} bait; 
  ";
  
  // concatenate these two strings to the variable $inventory to let them show up together on the screen
  if ($_SESSION['functional_fishing']['fire']) {
    $inventory .= "The fire is on";
  } else {
    $inventory .= "The fire is out.";
  }

  return $inventory;
}

/**
 * restart
 */
function restart() {
  clearSession();
  return "You have restarted the game.";
}

/**
 * Check for post command
 *  Check if the function exists for the command
 *    Update the responsewith function return value by calling the updateResponse function
 * Else return a 'not a valid command' response
 */

 if (isset($_POST['command'])) {
  //  instead of doing '$_POST['command' == 'fire']', we can use the function below to check if the function exsits
   if (function_exists($_POST['command'])) {
    // update response
    $response = $_POST['command']();// will call whatever function that $_POST['command'] is.
    updateResponse($response);
   } else {
    // not a valid function
    // updateResponse() is a custom function
    updateResponse("{$_POST['command']} is not a valid command.");
   }
 }