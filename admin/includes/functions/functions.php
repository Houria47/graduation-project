<?php
/*
 ** Title Function v0.0
 ** Title Function return Page Title in case the variable $pageTitle is set
 ** in the Page
 ** Return default title (Resto) in case the variable isn't set
 */
function getTitle()
{
  global $pageTitle;
  if (isset($pageTitle)) {
    echo $pageTitle;
  } else {
    echo 'Resto';
  }
}

/*
** IS Exist Function v0.0
** Function to check if a specific record is exist in database
**   depending on specific column value
** Accept 3 parameters:
** 1) $select : selected column to compare the value with it
** 2) $from : the table to select from
** 3) $value : the value to compare with
*/

function isExist($select , $from , $value){

  global $con;

  $stmt = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
  $stmt->execute([$value]);
  $count = $stmt->rowCount();
  return $count;
}
/*
** Remove Directory Function v0.0
** Function to check if a specific Directory is exist and 
**   delete it if so, with all its content,
** Accept 1 parameter:
** 1) $dir : the directory you want to delete
** retutn Boolean result
** true : if the directory deleted or if it's not exist
** false: otherwise
*/
function rrmdir($dir) {

  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {

      if ($object != "." && $object != "..") {

        if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);

      }
    }
    reset($objects);
    return rmdir($dir);
  }
  return true;
}

/*
** Time Ago Finction v0.0
** Function to get time as string descriping it 
** according to it's value
*/
// echo timeAgo("2023-6-16 6:27:00"); //The argument $time_ago is in timestamp (Y-m-d H:i:s)format.

function timeAgo($time_ago){
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        return "الآن";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            return "منذ دقيقة";
        }
        else{
            return "منذ $minutes د";
        }
    }
    //Hours
    else if($hours <24){
        if($hours==1){
            return "منذ ساعة";
        }else{
            return "منذ $hours سا";
        }
    }
    //Days
    else if($days < 7){
        if($days==1){
            return "البارحة";
        }else{
            return "منذ $days ي";
        }
    }
    //Weeks
    else if($weeks < 4){
        if($weeks==1){
            return "منذ أسبوع";
        }else{
            return "منذ $weeks أ";
        }
    }
    //Months
    else if($months <12){
        if($months==1){
            return "منذ شهر";
        }else{
            return "منذ $months ش";
        }
    }
    //Years
    else{
        if($years==1){
            return "منذ سنة";
        }else{
            return "منذ $years سنة";
        }
    }
}
/*
** Upload Authentication Files Function v0.0
** Function to Upload new restaurant authentication files to server folder
** Accept 2 parameters:
** 1) $restID : id of the restaurant account whom uploaded these files
**              to create folder with this id as a unique name
** 2) $files : file input value which holds information about the uploaded files
*/

function  uploadAuthenticationFiles($restID, $files){

  // image/png
  // application/pdf
  // Real path to use it in project files as relative path
  $filesPath = ROOTPATH . "uploads/authentication_files/";
  $relDirPath = $filesPath . $restID;
  // Create new folder in the given path $filesPath with the given id as name
  // TODO: make sure that the directory not exist already with is_dir()
  $loadDirpath = FULL_ROOTPATH . "uploads/authentication_files/" . $restID;
  mkdir($loadDirpath);

  $filesInfo = [];

  for($i = 0 ; $i < count($files['name']) ; $i++){
    $relPath = $relDirPath . "/" . $files['name'][$i];
    $loadPath = $loadDirpath . "/" . $files['name'][$i];
    // Alternative path if file type not image to show Alternative image
    $altPath = ROOTPATH . "layout/images/Standard/pdf.svg";
    if($files["type"][$i] == "image/png" || $files["type"][$i] == "image/jpeg" ){
      $altPath = $relPath;
    }
    move_uploaded_file($files['tmp_name'][$i],$loadPath);

    $filesInfo[] = [
      'name' => $files['name'][$i],
      'relPath' => $relPath,
      'altPath' => $altPath
    ]; 
  }

  $filesInfo = json_encode($filesInfo);
  return $filesInfo;
}

/*
** Get Users Information Function v0.0
** Function to get all users Information
*/
function getUsersInfo(){
  global $con;

    $stmt = $con->prepare("SELECT c.*,COUNT(DISTINCT resr.id) as restRevNum,
    COUNT(DISTINCT recr.id) as recpRevNum, COUNT(DISTINCT o.id) as ordersNum ,
    COUNT(DISTINCT reserv.id) as reservsNum ,
    COUNT(DISTINCT ad_comment.id) as commentsNum ,
    COUNT(DISTINCT ad_react.id) as reactsNum 
    FROM customer c LEFT OUTER  JOIN recipe_reviews recr ON recr.customer_id = c.id
    LEFT OUTER JOIN restaurant_reviews resr ON resr.customer_id = c.id
    LEFT OUTER  JOIN order_ o ON o.customer_id = c.id
    LEFT OUTER  JOIN reservation reserv ON reserv.customer_id = c.id
    LEFT OUTER JOIN ad_react ON ad_react.customer_id = c.id
    LEFT OUTER JOIN ad_comment ON ad_comment.customer_id = c.id
    WHERE c.is_admin = 0
    GROUP BY c.id
    ORDER BY c.created_at DESC;");
  $queryResult = $stmt->execute([]);

  if(!$queryResult || $stmt->rowCount() == 0)
    return [];

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach($rows as $row){
    $row['image'] = getImagePath($row['image'],"USER_PROFILE");
    $accounts[] = $row;
  }

  return $accounts;

}
function getUserVerifiedAccountsNum(){
  global $con;

  $stmt = $con->prepare("SELECT id FROM customer WHERE account_status = 1");
  $queryResult = $stmt->execute([]);

  if(!$queryResult)
    return false;
    
  return $stmt->rowCount();
}

/*
** Get Restaurants Information Function v0.0
** Function to get all restaurants Information, 
**  or one restaurants information if restID was specified.
** Accept 1 optional parameter:
** 1) $restID : id of the restaurant account, with default null value,
                to returns all restaurants account in such case.
** There are two helper function will be used in this function
    1) decodeRestAuthFiles : will decode files string and return an object
    2) getRestAddresses : will get restaurant addresses
*/
function getRestaurantsInfo($restID=NULL){
  global $con;

  $restCondition = $restID === NULL? '':'WHERE id = ?';
  $queryParam = $restID === NULL? []:[$restID];
  $query = "SELECT *
            FROM restaurant 
            $restCondition
            ORDER By created_at 
            DESC";
  $stmt = $con->prepare($query);
  $stmt->execute($queryParam);

  $accounts = [];
  if($stmt->rowCount()> 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      // calc auth files number
      $authFilesDecoded = json_decode($row['authentication_files']);// if there is no files result will be NULL
      $authFilesDecoded = $authFilesDecoded == NULL ? []: $authFilesDecoded;
      $RestAdds = getRestAddsNum($row['id']);
      
      $reviewsData = getRestaurantReviewsData($row['id']);
      $reviewsStatics = $reviewsData['statistcs'];
      $reviews = $reviewsData['reviews'];
  
      $accounts[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'email'=>$row['email'],
        'phone'=>$row["phone"],
        "description" => $row['description'],
        "open_time" => $row['open_time'],
        "close_time" => $row['close_time'],
        "delivery_service" => $row['delivery_service'],
        "reserv_service" =>  $row['reserv_service'],
        "fast_food" =>  $row['fast_food'],
        'profile_image' =>getImagePath($row['profile_image'],"REST_LOGO"),
        'cover_image' => getImagePath($row['cover_image'],"REST_BACK"),
        'created_at' => $row['created_at'],
        'account_status' => $row['account_status'],
        'postsNum' => $RestAdds['posts'],
        'recipesNum' => $RestAdds['recipes'],
        'listsNum' => $RestAdds['menus'],
        'delivary_fee' => $row['delivary_fee'],
        // calculated values
        'authentication_files' => decodeRestAuthFiles($row['authentication_files']),
        'addresses' => getRestAddresses($row['id']),
        'rate' => $reviewsStatics['averageRate'],
        'ratePercenatge' => $reviewsStatics['ratePercenatge'],
        'ratesNum' => $reviewsStatics['ratesNum'],
        'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
        'reviews' => $reviews
      ];
    }
    if($restID !== NULL){
      return $accounts[0];
    }
    return $accounts;
  }else{
    return false;
  }
}
function decodeRestAuthFiles($authFiles){
  $authFilesDecoded = json_decode($authFiles);
  $authFilesDecoded = $authFilesDecoded == NULL ? []: $authFilesDecoded;
  // convert object elements to associative array element
  $authFilesArray = [];
  foreach($authFilesDecoded as $objectFile){
    $arrayFile = [
      'name'=>$objectFile->name,
      'altPath' => $objectFile->altPath,
      'relPath' => $objectFile->relPath
    ];
    $authFilesArray[] = $arrayFile;
  }
  return $authFilesArray;
}
function getRestAddsNum($restID){
  global $con;
  $posts = $con->prepare("SELECT COUNT(id) as posts FROM advert WHERE restaurant_id =?");
  $menus = $con->prepare("SELECT COUNT(id) as menus FROM menu WHERE restaurant_id =?");
  $recipes = $con->prepare("SELECT COUNT(recipe.id) as recipes FROM recipe , menu 
  WHERE recipe.menu_id = menu.id 
  AND  menu.restaurant_id =?");

  $q1 = $posts->execute([$restID]);
  $q2 = $menus->execute([$restID]);
  $q3 = $recipes->execute([$restID]);

  if($q1 && $q2 && $q3){
    return [
      "posts" => $posts->fetch()['posts'],
      "menus" => $menus->fetch()['menus'],
      "recipes" => $recipes->fetch()['recipes'],
    ];
  }
  return [
    "posts" =>0,
    "menus" => 0,
    "recipes" => 0,
  ];
}
function getRestAddresses($restID){
  global $con;
  // get restaurant addresses
  $stmt = $con->prepare('SELECT address.id,state.id as state_id, state.name as state ,address.street, address.region 
                        FROM address JOIN state 
                        on address.state = state.id 
                        WHERE address.restaurant_id=?');

  $stmt->execute([$restID]);

  if($stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $addresses = [];
    foreach ($rows as $idx => $row){
      $addresses[]= [
        'id' => $row['id'],
        'state_id'=>$row['state_id'],
        'state' => $row['state'],
        'region' => $row['region'],
        'street' => $row['street']
      ];
    }
    return $addresses;
  }
  return [];
}
function getRestaurantReviewsData($restID){
  global $con;

  $reviewsData = [
    "statistcs" => [
      "ratePercenatge"=>0,
      "averageRate" =>0,
      "ratesNum" => 0,
      "ratesLevelsPercentage" => [0,0,0,0,0]
    ],
    "reviews" => []
  ];

  $rateSums = [0,0,0,0,0];
  $num_of_rates = 0;

  $stmt = $con->prepare("SELECT r.*,c.name ,c.id as customerID,c.image  FROM restaurant_reviews r 
  LEFT OUTER JOIN customer c 
  ON r.customer_id = c.id 
  WHERE restaurant_id = ?");
  
  $queryResult = $stmt->execute([$restID]);
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
      if(!empty($row['review'])){
        $reviewsData['reviews'][] =[
          "reviewID" => $row['id'],
          "reviewer_id" => $row['customerID'],
          "reviewer_name"=> $row['name'],
          "reviewer_image" => getImagePath($row['image'],"USER_PROFILE"),
          "given_rate" => $row['rate'],
          "review" => $row['review'],
          "added_at" => $row['added_at']
        ];
      }
       $num_of_rates++;
      $currentRate = $row['rate'];
      if($currentRate == 1 || $currentRate == 2 || $currentRate == 3 || $currentRate == 4 || $currentRate == 5){
        // add it only if it's accepted value to avoid array offset error,
        // ... well it must be an accepted value but i don't trust my code :( 
        $rateSums[$currentRate - 1]++;
      }
    }
  }
  $reviewsData['statistcs']['ratesNum'] = $num_of_rates;
  if($num_of_rates > 0){   
    $ratesStarsSum = $rateSums[0] + 2 * $rateSums[1] + 3 * $rateSums[2] + 4 * $rateSums[3] + 5 * $rateSums[4] ; 
    $reviewsData['statistcs']['averageRate'] = +bcdiv(($ratesStarsSum / $num_of_rates), 1, 1);
    $reviewsData['statistcs']['ratePercenatge']= ($reviewsData['statistcs']['averageRate'] / 5) * 100; 
    for($i=0; $i<5 ;$i++){
      $reviewsData['statistcs']['ratesLevelsPercentage'][$i] =  +bcdiv(($rateSums[$i] / $num_of_rates), 1, 1) * 100;
    }
  }else{
    $reviewsData['statistcs']['ratePercenatge']= 0;
  }
  return $reviewsData;
}
/*
** Get States Function v0.0
** Function to get all states from database
** return array with stats info if query succes
** return empty array otherwise
*/
function getStates(){
  // get states from database
  global $con;
  $stmt = $con->prepare('SELECT * FROM state');
  $stmt->execute([]);

  if($stmt->rowCount() > 0){
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }else{
    return [];
  }
}
/*
** Calculate Restaurants Statistics Function v0.0
** Function to Calculate Restaurants Statistics values
** this function used in getRestaurantsAccounts.php and restaurants tab in 
** admin dashboard for statistics purposes
** Accept 1 parameter:
** 1) $restaurants : restaurants accounts
*/
function calcRestaurantsSatistics($restaurants){
  $authenticatedNum = 0;
  $verifiedNum = 0;
  // get states from database
  global $con;
  $states = getStates();
  if(!empty($states)){
    $statesPercentage=[];
    $totalAddressesNumber = 0;
    foreach($states as $state){
      $statesPercentage += [
        $state['id'] => [
          'name' => $state['name'],
          'percentage' => 0
        ]];
    }
    foreach($restaurants as $restaurant){
      $status = $restaurant['account_status']; 
      if( $status == 1){
        $verifiedNum++;
      }
      if($status == 2){
        $authenticatedNum++;
      }
      // first, calculate the total number addresses and
      // the number of addrsses in each state
      foreach($restaurant['addresses'] as $address){
        $stateId = intval($address['state_id']);
        $statesPercentage[$stateId]['percentage']++;
        $totalAddressesNumber++;

      }
    }
    // second, calculate the percentage of each state
    foreach($statesPercentage as $stateID => $state){
      $numsOfRestaurantsInState = $state['percentage'] ;
      $percentage = ($numsOfRestaurantsInState / (float)$totalAddressesNumber) * 100;
      $percentageTruncated = +bcdiv($percentage, 1, 1);// to keep 1 digit after floating point
      $statesPercentage[$stateID]['percentage'] = $percentageTruncated;
    }
  }else{
    echo "error in file functions.php, can't get states from database line: " . __LINE__;
    exit();// to show the error and fix it in case getting data failed...
  }
  return [
    'authenticated' => $authenticatedNum,
    'verified' => $verifiedNum,
    'statesPercentage' => $statesPercentage
  ];
}
/*
 ** Get Image Path Function v0.0
 ** Function to get image full path and if it's not exist return 
 ** alternative image path which will determined by second paramter
 ** Paramters:
 ** 1) imageName : name of the image to find it and return full path of it
 ** 2) imageType : to return alternative image if the real image not exist
                   there is several types of images could be requested:
                   1- 'REST_BACK' : restaurant profile back image 
                   2- 'REST_LOGO' : restaurant profile logo image 
                   3- 'REST_POST' : restaurant post image 
                   4- 'REST_RECIPE' : restaurant recipe image 
                   5- 'REST_MENU' : restaurant menu image 
                   6- 'USER_PROFILE' : user profile image 
                   7- 'REACT' : Reaction image 
 */
function getImagePath($imageName, $imageType){
  $imagePath = "";
  switch($imageType){
    case "REST_BACK":
      $imagePath = ROOTPATH . "uploads/restaurant_profile_images/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/rest-back.jpg";
      }
      break;
    case "REST_LOGO":
      $imagePath = ROOTPATH . "uploads/restaurant_profile_images/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/rest-logo.jpg";
      }
      break;
    case "REST_RECIPE":
      $imagePath = ROOTPATH . "uploads/recipes/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/recipe-items.jpg";
      }
      break; 
    case "REST_MENU":
      $imagePath = ROOTPATH . "uploads/menus/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/menu-items.jpg";
      }
      break;
    case "USER_PROFILE":
      $imagePath = ROOTPATH . "uploads/customers/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/blank-profile-picture.jpg";
      }
      break;
    case "REST_POST":
      $imagePath = ROOTPATH . "uploads/ads/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/rest-logo.jpg";
      }
      break;
    case "REACT":
      $imagePath = ROOTPATH . "uploads/settings/reactions/" . $imageName;
      if(empty($imageName) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)){
        $imagePath = ROOTPATH . "layout/images/Standard/rest-logo.jpg";
      }
      break;
  }
  return $imagePath;
}
/*
 ** Date Format Function v0.0
 ** Paramters:
 ** 1) date : the date to format
 ** 2) pattern : see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#formatting-dates 
                  for pattern syntax
 */
function dateFormater($date,$pattern){
  $fmt = new \IntlDateFormatter('ar_AR', NULL, NULL);
  $fmt->setPattern($pattern);
  return $fmt->format(new \DateTime($date)); 
}
/*
 ** Delete Restaurant Files Function v0.0
 ** Function to delete restaurant files after account deletion
 ** Paramters:
 ** 1) restID : restaurant id to delete its files
 */
function deleteRestaurantFiles($restID){
  // using relative paths in this function works fine for ajax_request folder,
  // cuz all calls for this function are from ajax_request file
  
  // variable to indicate to which step file deletion reach
  $stepNumber = 0; // initaily, the deletion didn't work and result false
  // first remove authentication files
  $dir = './../../uploads/authentication_files/' . $restID;
  if(!empty($restID) && rrmdir($dir)){
    $stepNumber++;
  }
  // get restaurant information from DB for next steps
  global $con;
  $stmt = $con->prepare("SELECT profile_image, cover_image FROM restaurant WHERE id=?");
  $stmt->execute([$restID]);
  if($stmt->rowCount() > 0){
    // second remove profile images
    $row = $stmt->fetch();
    $profile_image = $row['profile_image'];
    $cover_image = $row['cover_image'];
    $profile_dir = FULL_ROOTPATH . 'uploads/restaurant_profile_images/' . $profile_image;
    $cover_dir = FULL_ROOTPATH . 'uploads/restaurant_profile_images/' . $cover_image;
    $coverFlag = $profileFlag = true;
    if(!empty($cover_image) && file_exists($cover_dir) && is_file($cover_dir)){
      $coverFlag = unlink($cover_dir)?$coverFlag:false;
    }
    if(!empty($profile_image) && file_exists($profile_dir) && is_file($profile_dir)){
      $profileFlag = unlink($profile_dir)?$coverFlag:false;
    }
    if($coverFlag && $profileFlag){
      $stepNumber++;
    }
  }
  // third remove posts images
  $postsFlag = true;
  $stmt = $con->prepare("SELECT media FROM advert WHERE restaurant_id=?");
  $stmt->execute([$restID]);
  if($stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $media = $row['media'];
      $mediaArray = explode(",",$media);

      $folderPath =  FULL_ROOTPATH . '/uploads/ads/';
      foreach($mediaArray as $image){
        $imagePath = $folderPath . $image;
        if(!empty($image) && file_exists($imagePath) && is_file($imagePath)){
          global $postsFlag ;
            $postsFlag=  unlink($imagePath)?$postsFlag:false;
        }
      }
    }
  }
  if($postsFlag)
    $stepNumber++;
  // fourth remove meals
  $mealsFlag = true;
  $stmt = $con->prepare("SELECT image FROM recipe 
  WHERE menu_id IN (SELECT id FROM menu WHERE restaurant_id = ?);");
  $stmt->execute([$restID]);
  if($stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $image = $row['image'];
      $imagePath =  FULL_ROOTPATH . '/uploads/recipes/' . $image;
      if(!empty($image) && file_exists($imagePath) && is_file($imagePath)){
          global $mealsFlag ;
          $mealsFlag = unlink($imagePath)?$mealsFlag:false;
      }
    }
  }
  if($mealsFlag)
    $stepNumber++;

  // fifth remove menus image
    $menusFlag = true;
    $stmt = $con->prepare("SELECT image FROM menu WHERE restaurant_id=?");
    $stmt->execute([$restID]);
    if($stmt->rowCount() > 0){
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($rows as $row){
        $image = $row['image'];
        $imagePath =  FULL_ROOTPATH . '/uploads/menus/' . $image;
        if(!empty($image) && file_exists($imagePath) && is_file($imagePath)){
            global $menusFlag ;
            $menusFlag = unlink($imagePath)?$menusFlag:false;
        }
      }
    }
    if($menusFlag){
      $menusFlag++;
    }
  return $stepNumber;
}


/*
** Get Destination Menu ID Function v0.0 
** Function to check if selected menu exist to Insert Recipe
** we have two cases:
** If $menuID == -1 then selected menu is the default menu
**    so we have to check if restaurant with given id has a default menu
**    and return its id if so, otherwise we have to create the default menu
** If $menuID != -1 then we check if the selected menu exist and return the same id if so
**    else we return default menu ID
** we will create subFunction to handle getting default menu id and to keep this function lean
 */
function getDestinationMenuID($restID, $menuID){
  global $con;
  if($menuID != -1){
    $stmt = $con->prepare("SELECT id FROM menu WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$menuID,$restID]);
    if($stmt->rowCount() > 0){
      return $menuID;
    }// else return default menu id
  }// else return default menu id

  return getDefaultMenuID($restID);
}
/*
** Get Default Menu ID Function v0.0
** Function to check if restaurant has a default menu or not
** return it's id if exist
** create the default menu if it's not exist 
*/
function getDefaultMenuID($restID){
  global $con;

  $stmt = $con->prepare("SELECT id FROM menu WHERE restaurant_id = ? AND is_default = ?");
  $stmt-> execute([$restID,true]);

  if($stmt->rowCount() > 0){
    return $stmt->fetch()['id'];
  }else{
    // create default menu
    $stmt = $con->prepare("INSERT INTO menu(name,restaurant_id, is_default) 
                                  VALUES(:name,:restID,:is_default)");
    $queryResualt = $stmt-> execute([
      "name" => 'وجبات أخرى',
      "restID" => $restID,
      "is_default" => true
    ]);
    if($queryResualt){
      return $con->lastInsertId();
    }else{
      return -1;// faild to create default menu
    }
  }
}

/*
** Get Reataurant Recipes Function v0.0
** Function to get all recipes for specific restauarant
** Parameters: 
** 1) restID: restaurant id which to get its recipes
** Return array with recipes information
*/
function getRestaurantRecipe($restID){
  global $con;

  $stmt = $con->prepare("SELECT * FROM recipe 
                            WHERE menu_id IN 
                                   (SELECT id FROM menu WHERE restaurant_id = ?) 
                            ORDER BY updated_at DESC");
  $queryResult = $stmt->execute([$restID]);
  if($queryResult){
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $recipesData = [];
    foreach($recipes as $recipe){
      $reviewsData = getRecipeReviewsData($recipe['id']);
      $reviewsStatics = $reviewsData['statistcs'];
      $reviews = $reviewsData['reviews'];

      $recipesData[] = [
          'id' => $recipe['id'],
          'name' => $recipe['name'],
          'menu_id' => $recipe['menu_id'],
          'description' => $recipe['description'],
          'price' => $recipe['price'],
          'discount' => $recipe['discount_price'],
          'availability' => $recipe['availability'],
          'created_at' => $recipe['created_at'],
          'updated_at' => $recipe['updated_at'],
          'tags' => $recipe['categories'],
          'image' =>  [
            "path" => getImagePath($recipe['image'],"REST_RECIPE"),
            "name" => $recipe['image']
          ],
          'ratePercenatge' => $reviewsStatics['ratePercenatge'],
          "averageRate" => $reviewsStatics['averageRate'],
          'ratesNum' => $reviewsStatics['ratesNum'],
          'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
          'reviews' => $reviews
      ];
    }
    return $recipesData;
  }
  return false;
}
function getRecipeInfo($recipeID){
  global $con;

  $stmt = $con->prepare("SELECT * FROM recipe WHERE id=?");
  $queryResult = $stmt->execute([$recipeID]);
  
  if($queryResult){
    $recipe = $stmt->fetch();
    $reviewsData = getRecipeReviewsData($recipe['id']);
    $reviewsStatics = $reviewsData['statistcs'];
    $reviews = $reviewsData['reviews'];

    $recipeData = [
        'id' => $recipe['id'],
        'name' => $recipe['name'],
        'description' => $recipe['description'],
        'price' => $recipe['price'],
        'discount' => $recipe['discount_price'],
        'availability' => $recipe['availability'],
        'created_at' => $recipe['created_at'],
        'updated_at' => $recipe['updated_at'],
        'menu_id' => $recipe['menu_id'],
        'tags' => $recipe['categories'],
        'image' =>  [
          "path" => getImagePath($recipe['image'],"REST_RECIPE"),
          "name" => $recipe['image']
        ],
        'ratePercenatge' => $reviewsStatics['ratePercenatge'],
        "averageRate" => $reviewsStatics['averageRate'],
        'ratesNum' => $reviewsStatics['ratesNum'],
        'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
        'reviews' => $reviews
    ];
    return $recipeData;
  }
  return false;
}
function getRecipeReviewsData($recipeID){
  global $con;

  $reviewsData = [
    "statistcs" => [
      "ratePercenatge"=>0,
      "averageRate" =>0,
      "ratesNum" => 0,
      "ratesLevelsPercentage" => [0,0,0,0,0]
    ],
    "reviews" => []
  ];

  $rateSums = [0,0,0,0,0];
  $num_of_rates = 0;

  $stmt = $con->prepare("SELECT r.*,c.name ,c.id as customerID,c.image  FROM recipe_reviews r 
  LEFT OUTER JOIN customer c 
  ON r.customer_id = c.id 
  WHERE recipe_id = ?");
  
  $queryResult = $stmt->execute([$recipeID]);
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
      if(!empty($row['review'])){
        $reviewsData['reviews'][] =[
          "reviewID" => $row['id'],
          "reviewer_id" => $row['customerID'],
          "reviewer_name"=> $row['name'],
          "reviewer_image" => getImagePath($row['image'],"USER_PROFILE"),
          "given_rate" => $row['rate'],
          "review" => $row['review'],
          "added_at" => $row['added_at']
        ];
      }
       $num_of_rates++;
      $currentRate = $row['rate'];
      if($currentRate == 1 || $currentRate == 2 || $currentRate == 3 || $currentRate == 4 || $currentRate == 5){
        // add it only if it's accepted value to avoid array offset error,
        // ... well it must be an accepted value but i don't trust my code :( 
        $rateSums[$currentRate - 1]++;
      }
    }
  }
  $reviewsData['statistcs']['ratesNum'] = $num_of_rates;
  if($num_of_rates > 0){   
    $ratesStarsSum = $rateSums[0] + 2 * $rateSums[1] + 3 * $rateSums[2] + 4 * $rateSums[3] + 5 * $rateSums[4] ; 
    $reviewsData['statistcs']['averageRate'] = +bcdiv(($ratesStarsSum / $num_of_rates), 1, 1);
    $reviewsData['statistcs']['ratePercenatge']= ($reviewsData['statistcs']['averageRate'] / 5) * 100; 
    for($i=0; $i<5 ;$i++){
      $reviewsData['statistcs']['ratesLevelsPercentage'][$i] =  +bcdiv(($rateSums[$i] / $num_of_rates), 1, 1) * 100;
    }
  }else{
    $reviewsData['statistcs']['ratePercenatge']= 0;
  }
  return $reviewsData;
}
/*
** Get Reataurant Menus Function v0.0
** Function to get all Menus for specific restauarant
** Parameters: 
** 1) restID: restaurant id which to get its Menus
** Return array with Menus
*/
function getRestaurantMenus($restID){
  global $con;
  $stmt = $con->prepare("SELECT * FROM menu WHERE restaurant_id = ?");
  $queryResult = $stmt->execute([$restID]);
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $menus = [];
    foreach($rows as $row){
    $stmt = $con->prepare("SELECT id,name,image,price,discount_price FROM recipe WHERE menu_id = ?");
    $queryResult = $stmt->execute([$row['id']]);
    $menu_recipes = [];
    if($queryResult){
      $menu_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
      $menus[] = [
        "id" => $row['id'],
        'restaurant_id' => $row['restaurant_id'],
        "name" => $row['name'],
        "description" => $row['description'],
        "is_default" => $row['is_default'],
        "image" => [
          "path" => getImagePath($row['image'],"REST_MENU"),
          "name" => $row['image']
        ],
        "recipes" => $menu_recipes
      ];
    }
    return $menus;
  }
  return false;
}
function getMenuInfo($menuID){
  global $con;
  $stmt = $con->prepare("SELECT * FROM menu WHERE id = ? LIMIT 1");
  $queryResult = $stmt->execute([$menuID]);
  if($queryResult){
    $row = $stmt->fetch();
    // get menu recipes
    $stmt = $con->prepare("SELECT id,name,image FROM recipe WHERE menu_id = ?");
    $queryResult = $stmt->execute([$menuID]);

    $menu_recipes = [];
    if($queryResult){
      $menu_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return [
      "id" => $row['id'],
      'restaurant_id' => $row['restaurant_id'],
      "name" => $row['name'],
      "description" => $row['description'],
      "is_default" => $row['is_default'],
      "image" => [
        "path" => getImagePath($row['image'],"REST_MENU"),
        "name" => $row['image']
      ],
      "recipes" => $menu_recipes
    ];
  }
  return false;
}
/*
** Get Reataurant Posts Function v0.0
** Function to get all Posts for specific restauarant
** Parameters: 
** 1) restID: restaurant id which to get its posts
** Return array with Posts
*/
function getRestaurantPosts($restID){
  global $con;

  $Posts = [];

  $ad_selects = "a.id, a.caption, a.media, a.created_at as ad_create,a.updated_at as ad_update";
  $ad_type_selects = "t.id as ad_type_id, t.name as ad_type_name";

  $stmt = $con->prepare("SELECT $ad_selects ,$ad_type_selects FROM advert a LEFT OUTER JOIN ad_type t ON a.ad_type = t.id WHERE a.restaurant_id = ? ORDER BY a.created_at DESC");
  $queryResult = $stmt->execute([$restID]);
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $Posts[] = [
        "id" => $row['id'],
        "caption" => $row['caption'],
        "media" => decodePostMedia($row['media']),
        "created_at" => $row['ad_create'],
        "updated_at" => $row['ad_update'],
        "type" => [      
          "id"=>$row['ad_type_id'],
          "name"=>$row['ad_type_name'],
        ],
        "reacts" => getPostReacts($row['id']),
        "comments" => getPostComments($row['id'])
      ];
    }
  }
  
  return $Posts;
}
function getPostInfo($postID){
  global $con;
  $Post = [];

  $ad_selects = "a.id, a.caption, a.media, a.created_at as ad_create,a.updated_at as ad_update";
  $ad_type_selects = "t.id as ad_type_id, t.name as ad_type_name";

  $stmt = $con->prepare("SELECT $ad_selects ,$ad_type_selects FROM advert a LEFT OUTER JOIN ad_type t ON a.ad_type = t.id WHERE a.id = ?");
  $queryResult = $stmt->execute([$postID]);
  if($queryResult){
    $row = $stmt->fetch();
      $Post = [
        "id" => $row['id'],
        "caption" => $row['caption'],
        "media" => decodePostMedia($row['media']),
        "created_at" => $row['ad_create'],
        "updated_at" => $row['ad_update'],
        "type" => [      
          "id"=>$row['ad_type_id'],
          "name"=>$row['ad_type_name'],
        ],
        "reacts" => getPostReacts($row['id']),
        "comments" => getPostComments($row['id'])
      ];
    }
  return $Post;
}
function decodePostMedia($media){
  if(empty($media)){
    return [];
  }
  $finalMedia = [];
  $mediaArray = explode(",",$media);

  foreach($mediaArray as $image){
    $finalMedia[] = getImagePath($image,"REST_POST");
  }
  
  return $finalMedia;
}

function getPostReacts($postID){
  global $con;
  $reacts = [];

  // get number of reacts
  $numberQuery= $con->prepare("SELECT id as reacts_number FROM ad_react WHERE advert_id = ?");
  $query1_Result = $numberQuery->execute([$postID]);

    // get top three reacts
  $top3Query =  $con->prepare("SELECT a.id, r.image , COUNT(a.id) FROM ad_react a
  LEFT OUTER JOIN react r 
  ON a.react_id = r.id
  WHERE advert_id = ?
  GROUP BY r.id
  ORDER BY COUNT(a.id)
  LIMIT 3;");

  $query2_Result = $top3Query->execute([$postID]);
  if($query1_Result && $query2_Result){
    $topThree = [];

    $rows = $top3Query->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $topThree[] = [
        "id" => $row['id'],
        "image" => getImagePath($row['image'],"REACT"),
      ];
    }

    $reacts = [
      "reacts_number" => $numberQuery->rowCount(),
      "top_three" => $topThree
    ];
  }

  return $reacts;

}
function getPostComments($postID){
  global $con;
  $comments = [];
  
  $customer_selects = "c.id as customer_id, c.name as customer_name,c.image as customer_image";
  $comment_selects = "com.id as comment_id , com.content ,com.comment_date, com.reply	,com.reply_date";

  $stmt = $con->prepare("SELECT $customer_selects, $comment_selects 
  FROM ad_comment com , customer c 
  WHERE com.customer_id = c.id
  AND com.advert_id = ? 
  ORDER BY com.comment_date DESC ");

  $queryRersult = $stmt->execute([$postID]);
  if($queryRersult){
    $rows =$stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
      $comments[] = [
        "id" => $row['comment_id'],
        "content" => $row['content'],
        "date" => $row['comment_date'],
        "reply" => $row['reply'],
        "reply_date" => $row['reply_date'],
        "customer" =>[
          "id" => $row['customer_id'],
          "name" => $row['customer_name'],
          "image" => getImagePath($row['customer_image'],"USER_PROFILE")
        ]
        ];
    }
  }

  return $comments;
}

function getRestaurantPostsEvents($restID){
  global $con;
  $reacts = [];
  $comments = [];
  
  $customer_selects = "c.id as customer_id, c.name as customer_name,c.image as customer_image";
  $comment_selects = "com.id as comment_id , com.content ,com.comment_date, com.reply	,com.reply_date";
  $react_selects = "r.name as react_name, r.image as react_image";

  $latestReactsQuery = $con->prepare("SELECT t.name as ad_type, ar.id as react_id ,ar.created_at as react_date, $customer_selects , $react_selects
  FROM customer c , advert ad LEFT OUTER JOIN ad_type t 
  ON ad.ad_type = t.id
  , ad_react ar LEFT OUTER JOIN react r 
  ON ar.react_id = r.id
  WHERE ar.customer_id = c.id
  AND ar.advert_id = ad.id
  AND ad.restaurant_id	= ?
  ORDER BY ar.created_at DESC
  LIMIT 10");

  $latestCommentsQuery = $con->prepare("SELECT t.name as ad_type, $customer_selects, $comment_selects 
  FROM ad_comment com , customer c ,advert ad LEFT OUTER JOIN ad_type t 
  ON ad.ad_type = t.id
  WHERE com.advert_id = ad.id
  AND com.customer_id = c.id
  AND ad.restaurant_id = ?
  ORDER BY com.comment_date DESC
  LIMIT 10");

$query1_result = $latestReactsQuery->execute([$restID]);
$query2_result = $latestCommentsQuery->execute([$restID]);

if($query1_result && $query2_result){
  $reactsRows = $latestReactsQuery->fetchAll(PDO::FETCH_ASSOC);
  $commentsRows = $latestCommentsQuery->fetchAll(PDO::FETCH_ASSOC);

  foreach($reactsRows as $react){
    $reacts[] = [
      "id" => $react['react_id'],
      "date" => $react['react_date'],
      "name" => $react['react_name'],
      "image" => getImagePath($react['react_image'],"REACT"),
      "ad_type" => $react['ad_type'],
      "customer" => [
        "id" => $react['customer_id'],
        "name" => $react['customer_name'],
        "image" => getImagePath($react['customer_image'],"USER_PROFILE")
      ]
    ];
  }
  foreach($commentsRows as $comment){
    $comments[] = [
      "id" => $comment['comment_id'],
      "date" => $comment['comment_date'],
      "content" => $comment['content'],
      "reply" => $comment['reply'],
      "reply_date" => $comment['reply_date'],
      "ad_type" => $comment['ad_type'],
      "customer" => [
        "id" => $comment['customer_id'],
        "name" => $comment['customer_name'],
        "image" => getImagePath($comment['customer_image'],"USER_PROFILE")
      ]
    ];
  }
}

return [
  "reacts" => $reacts,
  "comments" => $comments
];

}

function getAdTypes(){
  global $con;

  $types = [];

  $stmt = $con->prepare("SELECT t.* , COUNT(advert.id) as used_num 
  FROM ad_type t LEFT OUTER JOIN advert 
  ON advert.ad_type = t.id 
  GROUP BY t.id");
  $queryResult = $stmt->execute([]);
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $types[]=[
        "id" => $row['id'],
        "name" => $row['name'],
        "used_num" => $row['used_num']
      ];
    }
  }

  return $types;
}

function uploadPostMedai($media,$restID){
  $finalData = [];

  $folderPath = FULL_ROOTPATH . "uploads/ads/";
  for($i = 0 ; $i < count($media['name']) ; $i++){
    $finalImageName = $restID . "_" . rand(1,1000000) . "_" . $media["name"][$i];
    $loadPath = $folderPath . $finalImageName;
    if(!empty($media["name"][$i]) && is_dir($folderPath)){
      move_uploaded_file($media['tmp_name'][$i],$loadPath);
      $finalData[] = $finalImageName;
    }
    
  }

  return implode(",",$finalData);
}

function getUserInfo($userID){
  global $con;
  $userInfo = false;
  $stmt = $con->prepare("SELECT * FROM customer WHERE id=?");
  $stmt->execute([$userID]);

  if($stmt->rowCount() > 0){
    // Account exist, check if it confirmed
    $user = $stmt->fetch();
      // add user info to session
    $userInfo = [
      'id' => $user['id'],
      "name" => $user['name'],
      "email" => $user['email'],
      "phone" => $user['phone'],
      "image" => getImagePath($user['image'],"USER_PROFILE"),
      "is_admin" => $user['is_admin'],
      "account_status" => $user['account_status'],
      'created_at' => $user['created_at'],
      'address' => $user['address']
    ];
  }

  return $userInfo;
}

// function to get user orders num
function getUserCartItemsNum($userID){
  global $con;
  
  $stmt = $con->prepare("SELECT sum(quantity) as sum FROM cart WHERE customer_id = ?  GROUP BY customer_id");
  $queryResult = $stmt->execute([$userID]);
  if($queryResult && $stmt->rowCount() > 0){
    return $stmt->fetch()['sum'];
  }
  return 0;
}
// function to get all recipes in platform
// get one recipe if recipeID given
function getRecipes($recipeID = null){
  global $con;
  $recipeCondition = "";
  $recipeParam = [];
  if($recipeID != null){
    $recipeCondition = " WHERE r.id = ? "; 
    $recipeParam = [$recipeID];
  }
  $stmt = $con->prepare("SELECT r.*, 
  rest.name as restName,rest.id as restID,rest.profile_image as restImage ,rest.reserv_service,rest.delivery_service,rest.fast_food ,rest.cover_image as restCover,rest.phone,rest.open_time,rest.close_time
  FROM recipe r 
  LEFT OUTER JOIN menu m 
  ON r.menu_id = m.id 
  LEFT OUTER JOIN restaurant rest 
  ON m.restaurant_id = rest.id 
  $recipeCondition
  ORDER BY r.updated_at 
  DESC");

  $queryResult = $stmt->execute($recipeParam);
  
  if(!$queryResult){
    return false;
  }
  
  $recipesData = [];
  
  $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($recipes as $recipe){
    $reviewsData = getRecipeReviewsData($recipe['id']);
    $reviewsStatics = $reviewsData['statistcs'];
    $reviews = $reviewsData['reviews'];
    $recipesData[] = [
        'id' => $recipe['id'],
        'name' => $recipe['name'],
        'description' => $recipe['description'],
        'price' => $recipe['price'],
        'discount' => $recipe['discount_price'],
        'availability' => $recipe['availability'],
        'created_at' => $recipe['created_at'],
        'updated_at' => $recipe['updated_at'],
        'tags' => $recipe['categories'],
        'image' =>   getImagePath($recipe['image'],"REST_RECIPE"),
        'ratePercenatge' => $reviewsStatics['ratePercenatge'],
        'averageRate' => $reviewsStatics['averageRate'],
        'ratesNum' => $reviewsStatics['ratesNum'],
        'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
        'reviews' => $reviews,
        'menu_id' => $recipe['menu_id'],
        "restaurant" => [
          "id"=> $recipe['restID'],
          "name" => $recipe['restName'],
          "image" => getImagePath($recipe['restImage'],"REST_LOGO"),
          "cover" => getImagePath($recipe['restCover'],"REST_BACK"),
          "open_time" => $recipe['open_time'],
          "close_time" => $recipe['close_time'],
          "phone" => $recipe['phone'],
          "delivery_service" => $recipe['delivery_service'],
          "reserv_service" =>  $recipe['reserv_service'],
          "fast_food" =>  $recipe['fast_food'],
          "states" => getRestStates($recipe['restID']),
          "addresses" => getRestAddresses($recipe['restID'])
        ]
    ];
  }  
  if($recipeID != null){
    return $recipesData[0];
  }
  return $recipesData;
}
function getRestStates($restID){
  global $con;

  $stmt = $con->prepare("SELECT DISTINCT(state.id) FROM address LEFT OUTER JOIN state ON address.state = state.id WHERE address.restaurant_id = ?");
  $queryResult = $stmt->execute([$restID]);
  if($queryResult){
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  return false;
}
function getStatus(){
  global $con;

  $stmt = $con->prepare("SELECT * FROM state");
  $queryResult = $stmt->execute([]);
  $states = [];
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $states[] = [
        "id"=> $row["id"],
        "name"=> $row['name']
      ];
    }
  }
  return $states;
}

/*
** Get Top Rated Recipes Function v.0
** Function to get top rated recioes for specifc restaurant
** It takes restuarant id and return an array with top rated recipes
** Returned array has basic info of the recipe
*/
function getTopRatedRecipes($restID){
  global $con;
  $stmt = $con->prepare("SELECT review.rate,r.id as recipeID,r.name,r.image 
  FROM recipe_reviews review LEFT OUTER JOIN recipe r 
  ON review.recipe_id = r.id LEFT OUTER JOIN menu 
  ON r.menu_id = menu.id LEFT OUTER JOIN restaurant 
  ON menu.restaurant_id = restaurant.id 
  WHERE restaurant.id = ? 
  ORDER BY review.rate DESC 
  LIMIT 10;");

  $topRated = [];
  $queryResult = $stmt->execute([$restID]);
  if($queryResult && $stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
      if(!isset($topRated[$row['recipeID']])){
        $topRated += [$row['recipeID'] => [
          "id" => $row['recipeID'],
          "name" => $row['name'],
          "image" => getImagePath($row['image'],"REST_RECIPE"),
          "averageRate" => [0,0,0,0,0],
          "nums" => 0
          ]];
        }
      if(in_array($row['rate'],[1,2,3,4,5])){
        $topRated[$row['recipeID']]['averageRate'][$row['rate'] - 1]++;
        $topRated[$row['recipeID']]['nums']++;
      }
    }
  }
  foreach ($topRated as $key => $ele) {
    $sum = $ele['averageRate'][0] + 2*$ele['averageRate'][1] + 3*$ele['averageRate'][2] + 4*$ele['averageRate'][3] + 5*$ele['averageRate'][4];
    if($ele['nums'] > 0){
      $topRated[$key]['averageRate'] = +bcdiv(($sum / $ele['nums']), 1, 1);
    }else{
      $topRated[$key]['averageRate'] = 0;
    }
  } 

  return $topRated;
}
/*
** Get Latest Reviews on Recipes Function v.0
** Function to Latest Reviews on specifc restaurant's Recipes
** It takes restuarant id and return an array with Latest Reviews
*/
function getLatestReviewsOnRecipes($restID){
  global $con;
  $stmt = $con->prepare("SELECT review.rate,review.review,c.id as customerID,c.name as customerName,c.image,r.name as recipeName , r.id as recipeID
  FROM  customer c 
  LEFT OUTER JOIN recipe_reviews review
  ON review.customer_id = c.id LEFT OUTER JOIN recipe r 
  ON review.recipe_id = r.id LEFT OUTER JOIN menu 
  ON r.menu_id = menu.id LEFT OUTER JOIN restaurant 
  ON menu.restaurant_id = restaurant.id 
  WHERE restaurant.id = ?
  ORDER BY review.added_at DESC 
  LIMIT 10;");
  $latestReviews = [];
  $queryResult = $stmt->execute([$restID]);
  if($queryResult && $stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
      if(!empty($row['review'])){
        $latestReviews[] = [
          "reviewer_id" => $row['customerID'],
          "reviewer_name"=> $row['customerName'],
          "reviewer_image" => getImagePath($row['image'], "USER_PROFILE"),
          "given_rate" => $row['rate'],
          "recipe_name" => $row['recipeName'],
          "recipe_id" => $row['recipeID'],
          "review" => $row['review']
        ];
      }
    }
  }

  return $latestReviews;
}
function getMostDemandingRecipes($restID){
  global $con;
  $mostDemanding = [];

  $stmt = $con->prepare("SELECT COUNT(order_.id) as orders_num , COUNT(DISTINCT(c.id)) as customers_num, SUM(oi.quantity) as qty 
  ,rec.id as recipeID,rec.name ,rec.image 
    FROM customer c RIGHT OUTER JOIN order_ ON order_.customer_id = c.id
    JOIN order_status as os ON order_.order_status = os.id
    LEFT OUTER JOIN order_item oi ON oi.order_id = order_.id
    LEFT OUTER JOIN recipe rec ON oi.recipe_id = rec.id 
    LEFT OUTER JOIN menu m ON rec.menu_id = m.id 
    LEFT OUTER JOIN restaurant r ON m.restaurant_id = r.id
    WHERE r.id = ?
    AND os.id = 5
    GROUP BY rec.id,rec.name ,rec.image
    LIMIT 10;");

  $queryResult = $stmt->execute([$restID]);
  if($queryResult && $stmt->rowCount()>0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $mostDemanding[] = [
       "id" => $row['recipeID'],
       "image" => getImagePath($row['image'],"REST_RECIPE"),
       "name" => $row['name'],
       "qty" => $row['qty'],
       "orders_num" => $row['orders_num'],
       "customers_num" => $row['customers_num'],
      ];
    }
  }
  return $mostDemanding;
}
// function to get user ordres for a specific restaurant
// used from order page to send an order
function getUserRestOrder($restID,$userID){
  global $con;

  $stmt= $con->prepare("SELECT cart.* ,rec.price ,rec.discount_price
  , rec.image,rec.name as recipeName, r.delivary_fee
  FROM customer c 
  LEFT OUTER JOIN cart ON cart.customer_id = c.id 
  LEFT OUTER JOIN recipe rec ON cart.recipe_id = rec.id 
  LEFT OUTER JOIN menu m ON rec.menu_id = m.id 
  LEFT OUTER JOIN restaurant r ON m.restaurant_id = r.id 
  WHERE r.id = ? 
  AND c.id = ?;");  

  $queryResult = $stmt->execute([$restID,$userID]);

  $order = [];
  if($queryResult && $stmt->rowCount()>0){
    $order = [
      "total" => 0,
      "delivary_fee" => 0,
      "items" => [],
    ];
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $price = $row['price'];
      if($row['discount_price'] != null){
        $price = $row['discount_price'];
      }
      $order["items"][]=[
        "id" => $row['id'],
        "recipe" =>[
          "id" => $row['recipe_id'],
          "price" => $price,
          "name" => $row['recipeName'],
          "image" => getImagePath($row['image'],"REST_RECIPE")
        ],
        "quantity" => $row['quantity'],
        "add_date" => $row['add_date']
      ];
      $order['total'] += $price * $row['quantity'];
      $order['delivary_fee'] = $row['delivary_fee'];
    }
  }
  return $order;
}

// function to get user ordres from  all restaurants
// used from cart tab in user dashborad 
function getUserCart($userID){
  global $con;

  $stmt= $con->prepare("SELECT r.id as restID,r.name as restName , r.delivary_fee ,r.profile_image as restLogo,r.cover_image
  as restCover, r.open_time,r.close_time,r.phone as restPhone ,
  rec.id as recipeID,rec.name as recipeName, rec.description ,rec.price , rec.discount_price,rec.image as recipeImage,
  cart.id as cartItemID,cart.quantity,cart.add_date
  FROM customer JOIN cart ON cart.customer_id = customer.id 
  LEFT OUTER JOIN recipe rec ON cart.recipe_id = rec.id 
  LEFT OUTER JOIN menu ON rec.menu_id = menu.id 
  LEFT OUTER JOIN restaurant r ON menu.restaurant_id = r.id
  WHERE customer.id = ?");  

  $queryResult = $stmt->execute([$userID]);

  $cart = [];
  if($queryResult && $stmt->rowCount()>0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $price = $row['price'];
      if($row['discount_price'] != null){
        $price = $row['discount_price'];
      }
      $item = [
        "id" => $row['cartItemID'],
        "recipe" =>[
          "id" => $row['recipeID'],
          "price" => $row['price'],
          "discount_price" => $row['discount_price'],
          "description" => $row['description'],
          "name" => $row['recipeName'],
          "image" => getImagePath($row['recipeImage'],"REST_RECIPE")
        ],
        "quantity" => $row['quantity'],
        "add_date" => $row['add_date']
      ];

      if(isset($cart[$row['restID']])){
        $cart[$row['restID']]['items'][] = $item;
        $cart[$row['restID']]['total'] += $price *  $row['quantity'];
      }else{
        $cart += [$row['restID'] => [
          "total" => $price * $row['quantity'],
          "delivary_fee" => $row['delivary_fee'],
          "restaurant" => [
            "id" => $row['restID'],
            "name" => $row['restName'],
            "phone" => $row['restPhone'],
            "logo" => getImagePath($row['restLogo'],"REST_LOGO"),
            "cover" => getImagePath( $row['restCover'],"REST_BACK"),
            "open_time" => $row['open_time'],
            "close_time" => $row['close_time'],
            "addresses" => getRestAddresses($row['restID']),
          ],
          "items" => [$item]
        ]];
      }
    }
  }
  return $cart;
}
/*
** Get Restaurant Orders Function v0.0
** Get Orders of specific restuarant
**  Return array with orders info
*/
function getRestOrders($restID){
  global $con;

  $stmt = $con->prepare("SELECT o.id as orderID , o.order_date , o.address,o.phone,o.notes,
  os.id as statusID, os.status , c.name as customerName,c.image as customerImage
  FROM order_status os JOIN order_  o ON o.order_status = os.id
  LEFT OUTER JOIN customer c ON o.customer_id = c.id
  WHERE o.id IN (SELECT oi.order_id FROM order_item oi JOIN recipe rec ON oi.recipe_id = rec.id
                JOIN menu m ON rec.menu_id = m.id JOIN restaurant r ON m.restaurant_id = r.id
                WHERE r.id = ?)
  ORDER BY o.order_date DESC");
  $queryResult = $stmt->execute([$restID]);

  $orders =[
    'pending_orders' =>0,
    'processing_orders' => 0,
    'cancled_orders' =>0,
    'done_orders' => 0,
    'orders' => []
  ];
  if($queryResult && $stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $orderItems = getOrderItems($row['orderID']);
      $orders['orders'][]=[
        'id'=>$row['orderID'],
        'date' => $row['order_date'],
        'address' => $row['address'],
        'phone' => $row['phone'],
        'notes'=>$row['notes'],
        'status' =>[
          'id'=>$row['statusID'],
          'name' => $row['status']
        ],
        'customer_name' => $row['customerName'] ,
        'customer_image' => getImagePath($row['customerImage'],"USER_PROFILE") ,
        'items' => $orderItems['items'],
        'total' => $orderItems['total']
      ];
      if($row['statusID'] == 1){
        $orders['pending_orders']++;
      }
      if($row['statusID'] == 2 || $row['statusID'] == 3 ){
        $orders['processing_orders']++;
      }
      if($row['statusID'] == 4 || $row['statusID'] == 6 ){
        $orders['cancled_orders']++;
      }
      if($row['statusID'] == 5){
        $orders['done_orders']++;
      }
    }
  }
  return $orders;
}

function getOrderItems($orderID){
  global $con;

  $stmt = $con->prepare("SELECT rec.* , oi.quantity from recipe rec, order_item  oi
  WHERE oi.recipe_id = rec.id
  AND oi.order_id = ?");
  $queryResult = $stmt->execute([$orderID]);

  $orderItems = [
    'items' => [],
    "total" => 0
  ];
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $price = $row['price'];
      if($row['discount_price'] != null){
        $price = $row['price'];
      }
      $orderItems['items'][]=[
        'quantity' => $row['quantity'],
        "recipe_id" =>$row['id'],
        "name" =>$row['name'],
        "image" =>getImagePath($row['image'],"REST_RECIPE"),
        "price" =>$price
      ];
      $orderItems['total'] += $price * $row['quantity'];
    }
  }

  return $orderItems;
}

// Function to get settings reacts from database
function getReactsList(){
  global $con;

  $stmt = $con->prepare("SELECT r.* , COUNT(ad_react.id) as used_num 
  FROM react r LEFT OUTER JOIN ad_react 
  ON ad_react.react_id = r.id 
  GROUP BY r.id");
  $stmt->execute([]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $reacts = [];
  foreach($rows as $row){
    $reacts[] = [
      "id"=> $row['id'],
      "name"=> $row['name'],
      "image" => getImagePath($row['image'],"REACT"),
      "used_num" => $row['used_num']
    ];
  }

  return $reacts;
}

// function to get all posts
function getPosts(){
  global $con;

  $Posts = [];

  $ad_selects = "a.id, a.caption, a.media, a.created_at as ad_create,a.updated_at as ad_update";
  $ad_type_selects = "t.id as ad_type_id, t.name as ad_type_name";

  $stmt = $con->prepare("SELECT $ad_selects ,$ad_type_selects,
   r.name as restName,r.id as restID , r.profile_image
   FROM advert a LEFT OUTER JOIN ad_type t 
  ON a.ad_type = t.id 
  LEFT OUTER JOIN restaurant r ON a.restaurant_id = r.id
  ORDER BY a.created_at DESC");
  $queryResult = $stmt->execute();
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      
      $Posts[] = [
        "id" => $row['id'],
        "caption" => $row['caption'],
        "media" => decodePostMedia($row['media']),
        "created_at" => $row['ad_create'],
        "updated_at" => $row['ad_update'],
        "type" => [      
          "id"=>$row['ad_type_id'],
          "name"=>$row['ad_type_name'],
        ],
        "restName" => $row['restName'],
        "restID" => $row['restID'],
        "restImage" => getImagePath($row['profile_image'],"REST_LOGO"),
        "reacts" => getPostReacts($row['id']),
        "comments" => getPostComments($row['id'])
      ];
    }
  }
  
  return $Posts;
}

// function to get recipes with discount
function getRecipesOnSale(){
  
  global $con;

  $stmt = $con->prepare("SELECT r.*, 
  rest.name as restName,rest.id as restID,rest.profile_image as restImage ,rest.reserv_service,rest.delivery_service,rest.fast_food ,rest.cover_image as restCover,rest.phone,rest.open_time,rest.close_time
  FROM recipe r 
  LEFT OUTER JOIN menu m 
  ON r.menu_id = m.id 
  LEFT OUTER JOIN restaurant rest 
  ON m.restaurant_id = rest.id 
  WHERE r.discount_price IS NOT NULL
  ORDER BY r.updated_at 
  DESC");

  $queryResult = $stmt->execute([]);
  
  if(!$queryResult){
    return false;
  }
  
  $recipesData = [];
  
  $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($recipes as $recipe){
    $reviewsData = getRecipeReviewsData($recipe['id']);
    $reviewsStatics = $reviewsData['statistcs'];
    $reviews = $reviewsData['reviews'];
    $recipesData[] = [
        'id' => $recipe['id'],
        'name' => $recipe['name'],
        'description' => $recipe['description'],
        'price' => $recipe['price'],
        'discount' => $recipe['discount_price'],
        'availability' => $recipe['availability'],
        'created_at' => $recipe['created_at'],
        'updated_at' => $recipe['updated_at'],
        'tags' => $recipe['categories'],
        'image' =>   getImagePath($recipe['image'],"REST_RECIPE"),
        'ratePercenatge' => $reviewsStatics['ratePercenatge'],
        'averageRate' => $reviewsStatics['averageRate'],
        'ratesNum' => $reviewsStatics['ratesNum'],
        'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
        'reviews' => $reviews,
        'menu_id' => $recipe['menu_id'],
        "restaurant" => [
          "id"=> $recipe['restID'],
          "name" => $recipe['restName'],
          "image" => getImagePath($recipe['restImage'],"REST_LOGO"),
          "cover" => getImagePath($recipe['restCover'],"REST_BACK"),
          "open_time" => $recipe['open_time'],
          "close_time" => $recipe['close_time'],
          "phone" => $recipe['phone'],
          "delivery_service" => $recipe['delivery_service'],
          "reserv_service" =>  $recipe['reserv_service'],
          "fast_food" =>  $recipe['fast_food'],
          "states" => getRestStates($recipe['restID']),
          "addresses" => getRestAddresses($recipe['restID'])
        ]
    ];
  }
  return $recipesData;
}
// Function to get top rated restaurants
function getTopRatedRests(){
  global $con;

  $query = "SELECT r.* , COUNT(rr.id)
	  FROM restaurant r JOIN restaurant_reviews rr
    ON rr.restaurant_id = r.id
    GROUP BY r.id
    ORDER BY COUNT(rr.id)
    DESC
    LIMIT 6";
  $stmt = $con->prepare($query);
  $stmt->execute([]);

  $topRated = [];
  
  if($stmt->rowCount()> 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $RestAdds = getRestAddsNum($row['id']);
      
      $reviewsData = getRestaurantReviewsData($row['id']);
      $reviewsStatics = $reviewsData['statistcs'];
  
      $topRated[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'email'=>$row['email'],
        'phone'=>$row["phone"],
        "description" => $row['description'],
        "open_time" => $row['open_time'],
        "close_time" => $row['close_time'],
        "delivery_service" => $row['delivery_service'],
        "reserv_service" =>  $row['reserv_service'],
        "fast_food" =>  $row['fast_food'],
        'profile_image' =>getImagePath($row['profile_image'],"REST_LOGO"),
        'cover_image' => getImagePath($row['cover_image'],"REST_BACK"),
        'created_at' => $row['created_at'],
        'account_status' => $row['account_status'],
        'postsNum' => $RestAdds['posts'],
        'recipesNum' => $RestAdds['recipes'],
        'listsNum' => $RestAdds['menus'],
        'delivary_fee' => $row['delivary_fee'],
        // calculated values
        'addresses' => getRestAddresses($row['id']),
        'rate' => $reviewsStatics['averageRate'],
        'ratePercenatge' => $reviewsStatics['ratePercenatge'],
        'ratesNum' => $reviewsStatics['ratesNum'],
        'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
      ];
    }

  }
  return $topRated;
}

// 

function getRestResrvations($restID){
  global $con;

  $stmt = $con->prepare("SELECT r.id as reservID , r.name as reserv_name,r.reserv_date ,r.phone,r.notes,r.created_at,
  rs.id as statusID, rs.status , c.name as customerName,c.image as customerImage
  FROM reservation_status rs JOIN reservation r ON r.status = rs.id
  LEFT OUTER JOIN customer c ON r.customer_id = c.id
  WHERE r.restaurant_id = ?
  ORDER BY r.reserv_date DESC;");
  $queryResult = $stmt->execute([$restID]);

  $reservations =[
    'pending' =>0,
    'processing' => 0,
    'cancled' =>0,
    'done' => 0,
    'reservations' => []
  ];
  if($queryResult && $stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $orderItems = getReservItems($row['reservID']);
      $reservations['reservations'][]=[
        'id'=>$row['reservID'],
        'date' => $row['reserv_date'],
        'created_at' => $row['created_at'],
        'name' => $row['reserv_name'],
        'phone' => $row['phone'],
        'notes'=>$row['notes'],
        'status' =>[
          'id'=>$row['statusID'],
          'name' => $row['status']
        ],
        'customer_name' => $row['customerName'] ,
        'customer_image' => getImagePath($row['customerImage'],"USER_PROFILE") ,
        'items' => $orderItems['items'],
        'total' => $orderItems['total']
      ];
      if($row['statusID'] == 1){
        $reservations['pending']++;
      }
      if($row['statusID'] == 3 ){
        $reservations['processing']++;
      }
      if($row['statusID'] == 2 || $row['statusID'] == 5 ){
        $reservations['cancled']++;
      }
      if($row['statusID'] == 4){
        $reservations['done']++;
      }
    }
  }
  return $reservations;
}

function getReservItems($reservID){
  global $con;

  $stmt = $con->prepare("SELECT rec.* , ri.quantity from recipe rec, reservation_item  ri
  WHERE ri.recipe_id = rec.id
  AND ri.reservation_id = ?");
  $queryResult = $stmt->execute([$reservID]);

  $reservItems = [
    'items' => [],
    "total" => 0
  ];
  if($queryResult){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $price = $row['price'];
      if($row['discount_price'] != null){
        $price = $row['price'];
      }
      $reservItems['items'][]=[
        'quantity' => $row['quantity'],
        "recipe_id" =>$row['id'],
        "name" =>$row['name'],
        "image" =>getImagePath($row['image'],"REST_RECIPE"),
        "price" =>$price
      ];
      $reservItems['total'] += $price * $row['quantity'];
    }
  }

  return $reservItems;
}

// get user orders
function getOrders($userID){
  global $con;

  $stmt = $con->prepare("SELECT o.id as orderID ,o.name, o.order_date , o.address,o.phone,o.notes,
  os.id as statusID, os.status 
  FROM order_status os JOIN order_  o ON o.order_status = os.id
  LEFT OUTER JOIN customer c ON o.customer_id = c.id
  WHERE o.customer_id = ?
  ORDER BY o.order_date DESC");
  
  $queryResult = $stmt->execute([$userID]);

  $orders =[];
  if($queryResult && $stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $orderItems = getOrderItems($row['orderID']);
      $rest = getRestInfoForOrder($orderItems['items'][0]["recipe_id"]);
      $orders[]=[
        'id'=>$row['orderID'],
        'date' => $row['order_date'],
        'address' => $row['address'],
        'phone' => $row['phone'],
        'name' => $row['name'],
        'notes'=>$row['notes'],
        'status' =>[
          'id'=>$row['statusID'],
          'name' => $row['status']
        ],
        'items' => $orderItems['items'],
        'total' => $orderItems['total'],
        'restName' => $rest['name'],
        'restImage' =>getImagePath( $rest['profile_image'],"REST_LOGO"),
        'restFee' => $rest['delivary_fee'],
      ];
    }
  }
  return $orders;
}

function getRestInfoForOrder($recipeID){
  global $con;

  $stmt = $con->prepare("SELECT r.name, r.profile_image,r.delivary_fee
  FROM recipe rec JOIN menu m 
  ON rec.menu_id = m.id
  JOIN restaurant r 
  ON m.restaurant_id = r.id
  WHERE rec.id = ? 
  ");

  $stmt->execute([$recipeID]);

  return $stmt->fetch();
}

function getResrvations($userID){
  global $con;

  $stmt = $con->prepare("SELECT r.id as reservID , r.name as reserv_name,r.reserv_date ,r.phone,r.notes,r.created_at,res.name,res.profile_image,
  rs.id as statusID, rs.status , c.name as customerName,c.image as customerImage
  FROM reservation_status rs JOIN reservation r ON r.status = rs.id
  LEFT OUTER JOIN customer c ON r.customer_id = c.id
  JOIN restaurant res ON r.restaurant_id = res.id
  WHERE c.id = ?
  ORDER BY r.reserv_date DESC;");
  $queryResult = $stmt->execute([$userID]);

  $reservations =[  ];

  if($queryResult && $stmt->rowCount() > 0){
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
      $orderItems = getReservItems($row['reservID']);
      $reservations[]=[
        'id'=>$row['reservID'],
        'date' => $row['reserv_date'],
        'created_at' => $row['created_at'],
        'name' => $row['reserv_name'],
        'phone' => $row['phone'],
        'notes'=>$row['notes'],
        'status' =>[
          'id'=>$row['statusID'],
          'name' => $row['status']
        ],
        'customer_name' => $row['customerName'] ,
        'customer_image' => getImagePath($row['customerImage'],"USER_PROFILE") ,
        'items' => $orderItems['items'],
        'total' => $orderItems['total'],
        
        'restName' => $row['name'],
        'restImage' =>getImagePath( $row['profile_image'],"REST_LOGO"),
      ];
    }
  }
  return $reservations;
}

// function to get recipes with discount
function getTopRecipes(){

  global $con;

  $stmt = $con->prepare("SELECT COUNT(rr.id) as num, r.*, 
  rest.name as restName,rest.id as restID,rest.profile_image as restImage ,rest.reserv_service,rest.delivery_service,rest.fast_food ,rest.cover_image as restCover,rest.phone,rest.open_time,rest.close_time
  FROM recipe r 
  LEFT OUTER JOIN menu m 
  ON r.menu_id = m.id 
  LEFT OUTER JOIN restaurant rest 
  ON m.restaurant_id = rest.id 
  JOIN recipe_reviews rr ON rr.recipe_id = r.id
  GROUP BY r.id,restName, restID,restImage ,rest.reserv_service,rest.delivery_service,rest.fast_food ,restCover,rest.phone,rest.open_time,rest.close_time
  ORDER BY num,r.updated_at 
  DESC");

  $queryResult = $stmt->execute([]);
  
  if(!$queryResult){
    return false;
  }
  
  $recipesData = [];
  
  $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach($recipes as $recipe){
    $reviewsData = getRecipeReviewsData($recipe['id']);
    $reviewsStatics = $reviewsData['statistcs'];
    $reviews = $reviewsData['reviews'];
    $recipesData[] = [
        'id' => $recipe['id'],
        'name' => $recipe['name'],
        'description' => $recipe['description'],
        'price' => $recipe['price'],
        'discount' => $recipe['discount_price'],
        'availability' => $recipe['availability'],
        'created_at' => $recipe['created_at'],
        'updated_at' => $recipe['updated_at'],
        'tags' => $recipe['categories'],
        'image' =>   getImagePath($recipe['image'],"REST_RECIPE"),
        'ratePercenatge' => $reviewsStatics['ratePercenatge'],
        'averageRate' => $reviewsStatics['averageRate'],
        'ratesNum' => $reviewsStatics['ratesNum'],
        'ratesLevelsPercentage' => $reviewsStatics['ratesLevelsPercentage'],
        'reviews' => $reviews,
        'menu_id' => $recipe['menu_id'],
        "restaurant" => [
          "id"=> $recipe['restID'],
          "name" => $recipe['restName'],
          "image" => getImagePath($recipe['restImage'],"REST_LOGO"),
          "cover" => getImagePath($recipe['restCover'],"REST_BACK"),
          "open_time" => $recipe['open_time'],
          "close_time" => $recipe['close_time'],
          "phone" => $recipe['phone'],
          "delivery_service" => $recipe['delivery_service'],
          "reserv_service" =>  $recipe['reserv_service'],
          "fast_food" =>  $recipe['fast_food'],
          "states" => getRestStates($recipe['restID']),
          "addresses" => getRestAddresses($recipe['restID'])
        ]
    ];
  }
  return $recipesData;
}