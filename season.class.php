<?php
class Season{
  // Kadencja trwa od 1.10 do 30.09 roku następnego
  // Dla kadencji 1. (2015/2016) zwraca 2015
  public static function getYearForSeason($season){
    $year = $season + 2014;
    $curr_year = date('Y');
    while($curr_year - $year >= 24) $year += 32;
    return $year;
  }
  
  public static function getSeasonForYear($year){
    return ($year - 2014) % 32;
  }
  
  public static function getSchoolyearForSeason($season){
    $year = self::getYearForSeason($season);
    return $year.'/'.($year+1);
  }
  
  public static function getCurrent(){
    $year = date('Y');
    if(date('m') < 10) $year--;
    return self::getSeasonForYear($year);
  }

  public static function getYearForCurrentSeason(){
    return self::getYearForSeason(self::getCurrent());
  }

  public static function isFutureSeason($season){
    $y = self::getYearForSeason($season);
    if($y > date('Y')) return true;
    if($y == date('Y')) return date('m') < 10;
    return false;
  }
}
?>