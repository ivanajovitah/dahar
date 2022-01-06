<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use DateTimeImmutable;
use DateInterval;
use DatePeriod;

class Planner extends Controller
{
    public function Plan_Week_look($dateParam){
        date_default_timezone_set('Asia/Jakarta');
        $today = date(str_replace("_","-",$dateParam));
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($today);
        $endDate = $endDate->modify('+7 day');
        $lastWeek = $startDate->modify('-7 day')->format('Y_m_d');
        $nextWeek = $endDate->modify('+0 day')->format('Y_m_d');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getDataGenerate('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getDataGenerate('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getDataGenerate('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'id'=> $selectResepBreakfast->idMenu,
                    'nama_resep'=> $selectResepBreakfast->nama_resep,
                    'calories'=> $selectResepBreakfast->calories,
                    'carbs'=> $selectResepBreakfast->carbs,
                    'fat'=> $selectResepBreakfast->fat,
                    'protein'=> $selectResepBreakfast->protein,
                    'cover'=> $this->getCoverUrl($selectResepBreakfast->idMenu),
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'id'=> $selectResepLunch->idMenu,
                    'nama_resep'=> $selectResepLunch->nama_resep,
                    'calories'=> $selectResepLunch->calories,
                    'carbs'=> $selectResepLunch->carbs,
                    'fat'=> $selectResepLunch->fat,
                    'protein'=> $selectResepLunch->protein,
                    'cover'=> $this->getCoverUrl($selectResepLunch->idMenu),
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'id'=> $selectResepDinner->idMenu,
                    'nama_resep'=> $selectResepDinner->nama_resep,
                    'calories'=> $selectResepDinner->calories,
                    'carbs'=> $selectResepDinner->carbs,
                    'fat'=> $selectResepDinner->fat,
                    'protein'=> $selectResepDinner->protein,
                    'cover'=> $this->getCoverUrl($selectResepDinner->idMenu),
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        // dd($listGenerate);

        return view('dashboard.meal-planner_minggu',['listGenerate'=> $listGenerate, "lastWeek"=> $lastWeek , "nextWeek" => $nextWeek]);
    }

    public function Plan_Week(){
        // $startDate = date("Y-m-d");
        date_default_timezone_set('Asia/Jakarta');
        $today = date("Y-m-d");
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($today);
        $endDate = $endDate->modify('+7 day');
        $lastWeek = $startDate->modify('-7 day')->format('Y_m_d');
        $nextWeek = $endDate->modify('0 day')->format('Y_m_d');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getDataGenerate('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getDataGenerate('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getDataGenerate('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'id'=> $selectResepBreakfast->idMenu,
                    'nama_resep'=> $selectResepBreakfast->nama_resep,
                    'calories'=> $selectResepBreakfast->calories,
                    'carbs'=> $selectResepBreakfast->carbs,
                    'fat'=> $selectResepBreakfast->fat,
                    'protein'=> $selectResepBreakfast->protein,
                    'cover'=> $this->getCoverUrl($selectResepBreakfast->idMenu),
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'id'=> $selectResepLunch->idMenu,
                    'nama_resep'=> $selectResepLunch->nama_resep,
                    'calories'=> $selectResepLunch->calories,
                    'carbs'=> $selectResepLunch->carbs,
                    'fat'=> $selectResepLunch->fat,
                    'protein'=> $selectResepLunch->protein,
                    'cover'=> $this->getCoverUrl($selectResepLunch->idMenu),
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'id'=> $selectResepDinner->idMenu,
                    'nama_resep'=> $selectResepDinner->nama_resep,
                    'calories'=> $selectResepDinner->calories,
                    'carbs'=> $selectResepDinner->carbs,
                    'fat'=> $selectResepDinner->fat,
                    'protein'=> $selectResepDinner->protein,
                    'cover'=> $this->getCoverUrl($selectResepDinner->idMenu),
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        // dd($listGenerate);

        return view('dashboard.meal-planner_minggu',['listGenerate'=> $listGenerate, "lastWeek"=> $lastWeek , "nextWeek" => $nextWeek]);
    }

    public function Plan_Days_look($dateParam){
        date_default_timezone_set('Asia/Jakarta');
        $today = date(str_replace("_","-",$dateParam));
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($today);
        $endDate = $endDate->modify('+3 day');
        $lastWeek = $startDate->modify('-3 day')->format('Y_m_d');
        $nextWeek = $endDate->modify('+0 day')->format('Y_m_d');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getDataGenerate('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getDataGenerate('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getDataGenerate('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'id'=> $selectResepBreakfast->idMenu,
                    'nama_resep'=> $selectResepBreakfast->nama_resep,
                    'calories'=> $selectResepBreakfast->calories,
                    'carbs'=> $selectResepBreakfast->carbs,
                    'fat'=> $selectResepBreakfast->fat,
                    'protein'=> $selectResepBreakfast->protein,
                    'cover'=> $this->getCoverUrl($selectResepBreakfast->idMenu),
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'id'=> $selectResepLunch->idMenu,
                    'nama_resep'=> $selectResepLunch->nama_resep,
                    'calories'=> $selectResepLunch->calories,
                    'carbs'=> $selectResepLunch->carbs,
                    'fat'=> $selectResepLunch->fat,
                    'protein'=> $selectResepLunch->protein,
                    'cover'=> $this->getCoverUrl($selectResepLunch->idMenu),
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'id'=> $selectResepDinner->idMenu,
                    'nama_resep'=> $selectResepDinner->nama_resep,
                    'calories'=> $selectResepDinner->calories,
                    'carbs'=> $selectResepDinner->carbs,
                    'fat'=> $selectResepDinner->fat,
                    'protein'=> $selectResepDinner->protein,
                    'cover'=> $this->getCoverUrl($selectResepDinner->idMenu),
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        // dd($listGenerate);

        return view('dashboard.meal-planner_beberapaHari',['listGenerate'=> $listGenerate, "lastWeek"=> $lastWeek , "nextWeek" => $nextWeek]);
    }

    public function Plan_Days(){
        // $startDate = date("Y-m-d");
        date_default_timezone_set('Asia/Jakarta');
        $today = date("Y-m-d");
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($today);
        $endDate = $endDate->modify('+3 day');
        $lastWeek = $startDate->modify('-3 day')->format('Y_m_d');
        $nextWeek = $endDate->modify('+0 day')->format('Y_m_d');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getDataGenerate('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getDataGenerate('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getDataGenerate('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'id'=> $selectResepBreakfast->idMenu,
                    'nama_resep'=> $selectResepBreakfast->nama_resep,
                    'calories'=> $selectResepBreakfast->calories,
                    'carbs'=> $selectResepBreakfast->carbs,
                    'fat'=> $selectResepBreakfast->fat,
                    'protein'=> $selectResepBreakfast->protein,
                    'cover'=> $this->getCoverUrl($selectResepBreakfast->idMenu),
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'id'=> $selectResepLunch->idMenu,
                    'nama_resep'=> $selectResepLunch->nama_resep,
                    'calories'=> $selectResepLunch->calories,
                    'carbs'=> $selectResepLunch->carbs,
                    'fat'=> $selectResepLunch->fat,
                    'protein'=> $selectResepLunch->protein,
                    'cover'=> $this->getCoverUrl($selectResepLunch->idMenu),
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'id'=> $selectResepDinner->idMenu,
                    'nama_resep'=> $selectResepDinner->nama_resep,
                    'calories'=> $selectResepDinner->calories,
                    'carbs'=> $selectResepDinner->carbs,
                    'fat'=> $selectResepDinner->fat,
                    'protein'=> $selectResepDinner->protein,
                    'cover'=> $this->getCoverUrl($selectResepDinner->idMenu),
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        // dd($listGenerate);

        return view('dashboard.meal-planner_beberapaHari',['listGenerate'=> $listGenerate,"lastWeek"=> $lastWeek , "nextWeek" => $nextWeek]);
    }

    public function Plan_Day_look($dateParam){
        date_default_timezone_set('Asia/Jakarta');
        $today = date(str_replace("_","-",$dateParam));
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($today);
        $endDate = $endDate->modify('+1 day');
        $lastWeek = $startDate->modify('-1 day')->format('Y_m_d');
        $nextWeek = $endDate->modify('+0 day')->format('Y_m_d');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getDataGenerate('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getDataGenerate('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getDataGenerate('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'id'=> $selectResepBreakfast->idMenu,
                    'nama_resep'=> $selectResepBreakfast->nama_resep,
                    'calories'=> $selectResepBreakfast->calories,
                    'carbs'=> $selectResepBreakfast->carbs,
                    'fat'=> $selectResepBreakfast->fat,
                    'protein'=> $selectResepBreakfast->protein,
                    'cover'=> $this->getCoverUrl($selectResepBreakfast->idMenu),
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'id'=> $selectResepLunch->idMenu,
                    'nama_resep'=> $selectResepLunch->nama_resep,
                    'calories'=> $selectResepLunch->calories,
                    'carbs'=> $selectResepLunch->carbs,
                    'fat'=> $selectResepLunch->fat,
                    'protein'=> $selectResepLunch->protein,
                    'cover'=> $this->getCoverUrl($selectResepLunch->idMenu),
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'id'=> $selectResepDinner->idMenu,
                    'nama_resep'=> $selectResepDinner->nama_resep,
                    'calories'=> $selectResepDinner->calories,
                    'carbs'=> $selectResepDinner->carbs,
                    'fat'=> $selectResepDinner->fat,
                    'protein'=> $selectResepDinner->protein,
                    'cover'=> $this->getCoverUrl($selectResepDinner->idMenu),
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        // dd($listGenerate);

        return view('dashboard.meal-planner_hari',['listGenerate'=> $listGenerate, "lastWeek"=> $lastWeek , "nextWeek" => $nextWeek]);
    }

    public function Plan_Day(){
        // $startDate = date("Y-m-d");
        date_default_timezone_set('Asia/Jakarta');
        $today = date("Y-m-d");
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($today);
        $endDate = $endDate->modify('+1 day');
        $lastWeek = $startDate->modify('-1 day')->format('Y_m_d');
        $nextWeek = $endDate->modify('+0 day')->format('Y_m_d');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getDataGenerate('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getDataGenerate('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getDataGenerate('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'id'=> $selectResepBreakfast->idMenu,
                    'nama_resep'=> $selectResepBreakfast->nama_resep,
                    'calories'=> $selectResepBreakfast->calories,
                    'carbs'=> $selectResepBreakfast->carbs,
                    'fat'=> $selectResepBreakfast->fat,
                    'protein'=> $selectResepBreakfast->protein,
                    'cover'=> $this->getCoverUrl($selectResepBreakfast->idMenu),
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'id'=> $selectResepLunch->idMenu,
                    'nama_resep'=> $selectResepLunch->nama_resep,
                    'calories'=> $selectResepLunch->calories,
                    'carbs'=> $selectResepLunch->carbs,
                    'fat'=> $selectResepLunch->fat,
                    'protein'=> $selectResepLunch->protein,
                    'cover'=> $this->getCoverUrl($selectResepLunch->idMenu),
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'id'=> $selectResepDinner->idMenu,
                    'nama_resep'=> $selectResepDinner->nama_resep,
                    'calories'=> $selectResepDinner->calories,
                    'carbs'=> $selectResepDinner->carbs,
                    'fat'=> $selectResepDinner->fat,
                    'protein'=> $selectResepDinner->protein,
                    'cover'=> $this->getCoverUrl($selectResepDinner->idMenu),
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        // dd($listGenerate);

        return view('dashboard.meal-planner_hari',['listGenerate'=> $listGenerate,"lastWeek"=> $lastWeek , "nextWeek" => $nextWeek]);
    }

    public function Daftar_Belanja_GetData(Request $request){
        date_default_timezone_set('Asia/Jakarta');
        $today = date(str_replace("_","-",$request->startDate));
        $startDate = new DateTimeImmutable($today);
        $endDate = new DateTimeImmutable($request->endDate);
        $endDate = $endDate->modify('+7 day');
        
        $userId = Auth::id();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $listGenerate = [];
        foreach ($period as $dt) {
            $selectResepBreakfast = $this->getBahan('breakfast',$userId,$dt->format("Y-m-d"));
            $selectResepLunch = $this->getBahan('lunch',$userId,$dt->format("Y-m-d"));
            $selectResepDinner = $this->getBahan('dinner',$userId,$dt->format("Y-m-d"));
               
            //Check Breakfast
            if($selectResepBreakfast == null){
                $dataValue_Breakfast = "kosong";
            }
            else{
                $dataValue_Breakfast = array(
                    'bahan'=> $selectResepBreakfast->bahan,
                );
            }
            //Check Lunch
            if($selectResepLunch == null){
                $dataValue_Lunch = "kosong";
            }
            else{
                $dataValue_Lunch = array(
                    'bahan'=> $selectResepBreakfast->bahan,
                );
            }
            //Check Dinner
            if($selectResepDinner == null){
                $dataValue_Dinner = "kosong";
            }
            else{
                $dataValue_Dinner = array(
                    'bahan'=> $selectResepBreakfast->bahan,
                );
            }

            $meta = "";
            if($selectResepDinner == null){
                $meta = "kosong";
            }
            else{
                $meta = array(
                    'totalKalori' => $selectResepBreakfast->calories + $selectResepLunch->calories + $selectResepDinner->calories,
                    'totalCarbs' => $selectResepBreakfast->carbs + $selectResepLunch->carbs + $selectResepDinner->carbs,
                    'toatalProtein' => $selectResepBreakfast->protein + $selectResepLunch->protein + $selectResepDinner->protein ,
                    'toatalFat' => $selectResepBreakfast->fat + $selectResepLunch->fat + $selectResepDinner->fat ,
                );
            }

            $listGenerate[$dt->format("Y-m-d")] = array(
                'meta'=>$meta,
                'breakfast'=>$dataValue_Breakfast,
                'lunch'=>$dataValue_Lunch,
                'dinner'=>$dataValue_Dinner,
            );
        }

        $listBelanja = [];

        foreach($listGenerate as $data){
            if($data['meta'] != "kosong"){
                $bahan = json_decode($data['breakfast']['bahan']);
                foreach($bahan as $ingred){
                    if (array_key_exists($ingred->food,$listBelanja)){
                        $dataBahan = array(
                            'measure' => $listBelanja[$ingred->food]['measure'],
                            'qty' =>  $listBelanja[$ingred->food]['qty'] + $ingred->quantity
                        );

                        $listBelanja[$ingred->food] = $dataBahan;
                        
                    }else{
                        $dataBahan = array(
                            'measure' => $ingred->measure,
                            'qty' => $ingred->quantity
                        );
                        $listBelanja[$ingred->food] = $dataBahan;
                    }
                }
                
            }
        }

        ksort($listBelanja);

        // dd($listBelanja);
        
        return view('dashboard.daftar_belanja_show',[
            'listBelanja'=> $listBelanja, 
            'startDate' =>date("d M", strtotime($request->startDate)) , 
            'endDate'=>date("d M, Y", strtotime($request->endDate))
        ]);
    }

    public function Daftar_Belanja(){
        return view('dashboard.daftar_belanja');
    }

    public function getCoverUrl($idCariResep){
        $select = DB::table('list_resep')
        ->where('id','=',$idCariResep)
        ->first();

        return $select->cover;
    }

    public function getBahan($group, $idUser , $forDate){
        return DB::table('generate')
                ->join('list_resep', 'list_resep.id', '=', 'generate.idMenu')
                ->where('generate.idUser','=',$idUser)
                ->where('generate.forDate','=',$forDate)
                ->where('generate.groupMenu', '=', $group)
                ->orderBy('generate.created_at', 'desc')
                ->orderBy('generate.forDate', 'asc')
                ->first();
    }


    public function getDataGenerate($group, $idUser , $forDate){
        return DB::table('generate')
                ->where('idUser','=',$idUser)
                ->where('forDate','=',$forDate)
                ->where('groupMenu', '=', $group)
                ->orderBy('created_at', 'desc')
                ->orderBy('forDate', 'asc')
                ->first();
    }
}
