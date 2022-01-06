<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use DateTime;

class GeneratePlan extends Controller
{
    public function hitung_umur($tanggal_lahir){
        $birthDate = new DateTime($tanggal_lahir);
        $today = new DateTime("today");
        if ($birthDate > $today) { 
            exit("0 tahun 0 bulan 0 hari");
        }
        $y = $today->diff($birthDate)->y;
        $m = $today->diff($birthDate)->m;
        $d = $today->diff($birthDate)->d;
        return array('tahun' => $y, 'bulan'=>$m , 'hari'=>$d);
    }

    public function getTDEE(){
        $userId = Auth::id();
        $userProfileSelect = DB::table('user_profile')
                        ->select("*")
                        ->where('idUser', $userId)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

        $selectUsers = DB::table('users')
                        ->select("jenis_kelamin","tanggal_lahir")
                        ->where('id', $userId)
                        ->first();


        $tinggiBadan = $userProfileSelect->tinggi_badan;
        $beratBadan = $userProfileSelect->berat_badan;
        $tingkat_aktivitas = $userProfileSelect->tingkat_aktivitas;
        $goal = $userProfileSelect->goal;
        $gender = $selectUsers->jenis_kelamin;
        $tglLahir = $selectUsers->tanggal_lahir;
        $dataUsia = $this->hitung_umur($tglLahir);
        $usia = $dataUsia['tahun'];

        $bmr = "";
        $tdee = "";
        //BMR
        if($gender == "M"){
            $bmr = (10 * $beratBadan) + (6.25 * $tinggiBadan) - (5 * $usia) + 5;
        }
        elseif($gender == "F"){
            $bmr = (10 * $beratBadan) + (6.25 * $tinggiBadan) - (5 * $usia) - 161;
        }

        //tdee bersih
        if($tingkat_aktivitas == 1){
            $tdee = $bmr * 1.2;
        }
        elseif($tingkat_aktivitas == 2){
            $tdee = $bmr * 1.37;
        }
        elseif($tingkat_aktivitas == 3){
            $tdee = $bmr * 1.55;
        }
        elseif($tingkat_aktivitas == 4){
            $tdee = $bmr * 1.725;
        }
        elseif($tingkat_aktivitas == 5){
            $tdee = $bmr * 1.9;
        }

        //tdee goal
        if($goal == 'maintain'){
            $tdeeBersih = $tdee;
        }
        elseif($goal == 'cutting'){
            $tdeeBersih = $tdee - 500;
        }
        elseif($goal == 'bulking'){
            $tdeeBersih = $tdee + 500;
        }

        return array('bmr'=>$bmr, 'tdee'=>$tdee, 'tdeeBersih'=>$tdeeBersih, 'usia'=>$dataUsia);
        
    }


    public function getTDEE_other($tinggi,$berat,$aktivitas,$goals,$gender,$usia){
        
        $tinggiBadan = $tinggi;
        $beratBadan = $berat;
        $tingkat_aktivitas = $aktivitas;
        $goal = $goals;
        $gender = $gender;
        $dataUsia = $usia;

        $bmr = "";
        $tdee = "";
        //BMR
        if($gender == "M"){
            $bmr = (10 * $beratBadan) + (6.25 * $tinggiBadan) - (5 * $usia) + 5;
        }
        elseif($gender == "F"){
            $bmr = (10 * $beratBadan) + (6.25 * $tinggiBadan) - (5 * $usia) - 161;
        }

        //tdee bersih
        if($tingkat_aktivitas == 1){
            $tdee = $bmr * 1.2;
        }
        elseif($tingkat_aktivitas == 2){
            $tdee = $bmr * 1.37;
        }
        elseif($tingkat_aktivitas == 3){
            $tdee = $bmr * 1.55;
        }
        elseif($tingkat_aktivitas == 4){
            $tdee = $bmr * 1.725;
        }
        elseif($tingkat_aktivitas == 5){
            $tdee = $bmr * 1.9;
        }

        //tdee goal
        if($goal == 'maintain'){
            $tdeeBersih = $tdee;
        }
        elseif($goal == 'cutting'){
            $tdeeBersih = $tdee - 500;
        }
        elseif($goal == 'bulking'){
            $tdeeBersih = $tdee + 500;
        }

        return array('bmr'=>$bmr, 'tdee'=>$tdee, 'tdeeBersih'=>$tdeeBersih, 'usia'=>$dataUsia);
        
    }

    public function generateFeedbackSave(Request $request){
        DB::table('result_feedback')
              ->where('id', $request['id_resultFeedback'])
              ->update(['feedback' => $request['star-rating']]);
        return redirect('/planner-week');
    }
    
    public function generateSave(Request $request){
        $userId = Auth::id();

        $insertData = [];
        $listResep = $request['listResep'];
        foreach($listResep as $date => $value){
            $forDate = str_replace("_","-",$date);

            $breakfastResep = $this->summary_getResepData($value['breakfast']);
            $lunchResep = $this->summary_getResepData($value['lunch']);
            $dinnerResep = $this->summary_getResepData($value['dinner']);

            $id_resultFeedback = DB::table('result_feedback')->insertGetId(
                array('feedback' => '-1','created_at' =>date("Y-m-d"),)
            );

        
            $dataBreakfast = array(
                'idUser'=> $userId, 
                'forDate' => $forDate,
                'groupMenu' => 'breakfast',
                'idMenu' => $value['breakfast'],
                'nama_resep' => $breakfastResep['judul_resep'],
                'calories' => $breakfastResep['calories'],
                'carbs' => $breakfastResep['macro']['karbo'],
                'fat' => $breakfastResep['macro']['lemak'],
                'protein' => $breakfastResep['macro']['protein'],
                'feedback' => "-1",
                'id_resultFeedback' => $id_resultFeedback,
                'created_at' =>date("Y-m-d")
            );
            array_push($insertData, $dataBreakfast);

            $dataLunch = array(
                'idUser'=> $userId, 
                'forDate' => $forDate,
                'groupMenu' => 'lunch',
                'idMenu' => $value['lunch'],
                'nama_resep' => $lunchResep['judul_resep'],
                'calories' => $lunchResep['calories'],
                'carbs' => $lunchResep['macro']['karbo'],
                'fat' => $lunchResep['macro']['lemak'],
                'protein' => $lunchResep['macro']['protein'],
                'feedback' => "-1",
                'id_resultFeedback' => $id_resultFeedback,
                'created_at' =>date("Y-m-d")
            );
            array_push($insertData, $dataLunch);

            $dataDinner = array(
                'idUser'=> $userId, 
                'forDate' => $forDate,
                'groupMenu' => 'dinner',
                'idMenu' => $value['dinner'],
                'nama_resep' => $dinnerResep['judul_resep'],
                'calories' => $dinnerResep['calories'],
                'carbs' => $dinnerResep['macro']['karbo'],
                'fat' => $dinnerResep['macro']['lemak'],
                'protein' => $dinnerResep['macro']['protein'],
                'feedback' => "-1",
                'id_resultFeedback' => $id_resultFeedback,
                'created_at' =>date("Y-m-d")
            );
            array_push($insertData, $dataDinner);
        }

        DB::table('generate')->insert($insertData);

        
        return view('dashboard.generate_feedback',['id_resultFeedback'=> $id_resultFeedback]);
    }

    public function generatePilih(Request $request){
        $listBreakfast = $request['pilihBreakfast'];
        $listLunch = $request['pilihLunch'];
        $listDinner = $request['pilihDinner'];

        $listPlan = [];
        foreach($listBreakfast as $date=>$value){
            $resepData_Breakfast = $this->summary_getResepData($listBreakfast[$date]);
            $resepData_Lunch = $this->summary_getResepData($listLunch[$date]);
            $resepData_Dinner = $this->summary_getResepData($listDinner[$date]);

            $thatDay = array(
                "breakfast" =>$resepData_Breakfast,
                "lunch" =>$resepData_Lunch,
                "dinner" =>$resepData_Dinner,
            );

            $listPlan[$date] = $thatDay;
        }
        
        return view('dashboard.generate_summary',['listPlan'=> $listPlan]);
    }

    public function summary_getResepData($id){
        $selectResep = DB::table('list_resep')
                ->select(
                    'id','judul_resep','cover','healthLabels','calories',
                    'yield','totalTime', 'cuisineType','digest','score','likes')
                ->where('id', $id)
                ->first();

        $idResep = $selectResep->id;
        $judulResep = $selectResep->judul_resep;
        $coverResep = $selectResep->cover;
        $jumlahServingResep = $selectResep->yield;
        $cuisineTypeResep = $selectResep->cuisineType;
        $caloriesResep = ($selectResep->calories)/$jumlahServingResep;

        $fat = "";
        $protein = "";
        $carbs = "";
        foreach (json_decode ($selectResep->digest) as $macros){
            if($macros->label == "Fat"){
                $fat = ($macros->total)/$jumlahServingResep;
            }
            elseif($macros->label == "Protein"){
                $protein = ($macros->total)/$jumlahServingResep;;
            }
            elseif($macros->label == "Carbs"){
                $carbs = ($macros->total)/$jumlahServingResep;;
            }
        }

        $resepData = array(
            'idResep'=> $idResep,
            'judul_resep'=> $judulResep,
            'cover'=> $coverResep,
            'totalWeight'=> $jumlahServingResep,
            'calories'=> $caloriesResep,
            'cuisineTypeResep'=> $cuisineTypeResep,
            'macro'=> array(
                'karbo'=> $carbs,
                'lemak'=> $fat,
                'protein'=> $protein,
            )
        );

        return $resepData;
    }

    public function generateStart(){
        $userId = Auth::id();

        $userProfileSelect = DB::table('user_profile')
                        ->select("*")
                        ->where('idUser', $userId)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

        $selectUsers = DB::table('users')
                        ->select("jenis_kelamin","tanggal_lahir")
                        ->where('id', $userId)
                        ->first();

        $meta = "";

        $gender = $selectUsers->jenis_kelamin;
        $tglLahir = $selectUsers->tanggal_lahir;


        if($userProfileSelect == null || $gender == null || $tglLahir == null){
           $meta =  "kosong";
        }
        else{
            $meta = "ada";
        }

        return view('dashboard.generate',['meta'=> $meta]);
    }

    public function generate(Request $request){
        $userId = Auth::id();
        $nutrisi = $this -> getTDEE();

        $userProfileSelect = DB::table('user_profile')
                        ->select("*")
                        ->where('idUser', $userId)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

        $pref = $userProfileSelect->orientasi_makanan;

        $komposisiPilih = $request["komposisi"];
        $startDate = new DateTimeImmutable($request["startDate"]);
        $endDate = new DateTimeImmutable($request["endDate"]);
        $endDate = $endDate->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $list_Breakfast =  array();
        $list_Lunch =  array();
        $list_Dinner =  array();

        $rekomendasi_akhir = array();

        foreach ($period as $dt) { 
            $AHP_Breakfast = $this->AHP_SORT($userId,$nutrisi['tdeeBersih'],$pref,$komposisiPilih,"breakfast");
            $AHP_Lunch = $this->AHP_SORT($userId,$nutrisi['tdeeBersih'],$pref,$komposisiPilih,"lunch");
            $AHP_Dinner = $this->AHP_SORT($userId,$nutrisi['tdeeBersih'],$pref,$komposisiPilih,"dinner");

            $list_Breakfast = $AHP_Breakfast;
            $list_Lunch = $AHP_Lunch;
            $list_Dinner = $AHP_Dinner;

            $setBaru = array();
            $dataSetBaru = array();

            foreach($list_Breakfast as $menuBreakfast){
                foreach($list_Lunch as $menuLunch){
                    foreach($list_Dinner as $menuDinner){
                        $b_karbo = $menuBreakfast['macro']['karbo'] * 4;
                        $b_protein = $menuBreakfast['macro']['protein'] * 4;
                        $b_lemak = $menuBreakfast['macro']['lemak'] * 9;
                        $b_kalori = $b_karbo + $b_lemak + $b_protein;

                        $l_karbo = $menuLunch['macro']['karbo'] * 4;
                        $l_protein = $menuLunch['macro']['protein'] * 4;
                        $l_lemak = $menuLunch['macro']['lemak'] * 9;
                        $l_kalori = $l_karbo + $l_lemak + $l_protein;

                        $d_karbo = $menuDinner['macro']['karbo'] * 4;
                        $d_protein = $menuDinner['macro']['protein'] * 4;
                        $d_lemak = $menuDinner['macro']['lemak'] * 9;
                        $d_kalori = $d_karbo + $d_lemak + $d_protein;

                        $kalori_set = $b_kalori + $l_kalori + $d_kalori;

                        $b_data = array(
                            "data_resep"=>$menuBreakfast,
                            "karbo"=>$b_karbo, 
                            "protein" => $b_protein, 
                            "fat" => $b_lemak, 
                            "kalori" => $b_kalori
                        );

                        $l_data = array(
                            "data_resep"=>$menuLunch,
                            "karbo"=>$l_karbo, 
                            "protein" => $l_protein, 
                            "fat" => $l_lemak, 
                            "kalori" => $l_kalori
                        );

                        $d_data = array(
                            "data_resep"=>$menuDinner,
                            "karbo"=>$d_karbo, 
                            "protein" => $d_protein, 
                            "fat" => $d_lemak, 
                            "kalori" => $d_kalori
                        );

                        $dataSET = array(
                            "breakfast"=>$b_data,
                            "lunch"=>$l_data,
                            "dinner"=>$d_data,
                        );
                        array_push($setBaru,$kalori_set);
                        array_push($dataSetBaru,$dataSET);
                    }
                }
            }

            $jumlahPairingAwal = count($setBaru);

            $batasAtas_TDEE = $nutrisi['tdeeBersih']+($nutrisi['tdeeBersih']*(10/100));
            $batasBawah_TDEE = $nutrisi['tdeeBersih']-($nutrisi['tdeeBersih']*(10/100));
            $setMemenuhi_kriteria = array();
            foreach($setBaru as $key => $set){
                if( ($set >= $batasBawah_TDEE) && ($set <= $batasAtas_TDEE)){
                    array_push($setMemenuhi_kriteria,$key);
                }
            }
            $jumlahMemenuhiKriteria = count($setMemenuhi_kriteria);

            //Randome Pick 10
            if(count($setMemenuhi_kriteria)!= 0){
                if(count($setMemenuhi_kriteria)<10){
                    $jumlahRand = count($setMemenuhi_kriteria);
                    // dapat Arraynya
                    $rand_keys = array_rand($setMemenuhi_kriteria, $jumlahRand);
                    $sampleMenu = [];
                    foreach($rand_keys as $index){
                        array_push($sampleMenu, $setMemenuhi_kriteria[$index]);
                    }
                }
                else{
                    $jumlahRand = 10;
                    // dapat Arraynya
                    $rand_keys = array_rand($setMemenuhi_kriteria, $jumlahRand);
                    $sampleMenu = [];
                    foreach($rand_keys as $index){
                        array_push($sampleMenu, $setMemenuhi_kriteria[$index]);
                    }
                }
                $rekomen_final = array();
                $rekomen_final_detail = array();

                foreach($sampleMenu as $index){
                    array_push($rekomen_final, $setBaru[$index]);
                    array_push($rekomen_final_detail, $dataSetBaru[$index]);
                }
                $rekomendasi_akhir[$dt->format("Y_m_d")] = $rekomen_final_detail;
            }
            
        }
        
        // dd($rekomendasi_akhir);
        
        return view('dashboard.generate_menu',
            [   'rekomendasi_akhir'=> $rekomendasi_akhir,
            ]);
    }

    public function AHP_SORT($id_User, $TDEE, $preferance, $tipe_limitProfile,$template_group){
        $idUser = $id_User;
        $tdee = $TDEE;
        $pref = $preferance;
        $pilihTipe = $tipe_limitProfile;
        $groupTemplate = $template_group;

        $limitinProfileTipe = array( // karbohidrat , protein, lemak
            "highKarbo" => array(
                "karbo" => round((($tdee * (50/100))/4),3),
                "protein" => round((($tdee * (30/100))/4),3),
                "lemak" => round((($tdee * (20/100))/9),3),
            ),
            "moderateKarbo" => array(
                "karbo" => round((($tdee * (35/100))/4),3),
                "protein" => round((($tdee * (30/100))/4),3),
                "lemak" => round((($tdee * (35/100))/9),3),
            ),
            "lowKarbo" => array(
                "karbo" => round((($tdee * (20/100))/4),3),
                "protein" => round((($tdee * (40/100))/4),3),
                "lemak" => round((($tdee * (40/100))/9),3),
            ),
        );

        $selectResep = "";

        if ($pref == "normal"){
            $selectResep =  DB::table('list_resep')
                            ->select(
                                'id','judul_resep','cover','healthLabels','calories',
                                'yield','totalTime', 'cuisineType','digest','score','likes')
                            ->where('cuisineType', 'LIKE', '%'.$groupTemplate.'%')
                            ->get();
        }
        elseif($pref == "vegetarian"){
            $selectResep =  DB::table('list_resep')
                            ->select(
                                'id','judul_resep','cover','healthLabels','calories',
                                'yield','totalTime', 'cuisineType','digest','score','likes')
                            ->where('healthLabels', 'LIKE', '%vegetarian%')
                            ->where('cuisineType', 'LIKE', '%'.$groupTemplate.'%')
                            ->get();
        }
        elseif($pref == "vegan"){
            $selectResep =  DB::table('list_resep')
                            ->select(
                                'id','judul_resep','cover','healthLabels','calories',
                                'yield','totalTime', 'cuisineType','digest','score','likes')
                            ->where('healthLabels', 'LIKE', '%vegan%')
                            ->where('cuisineType', 'LIKE', '%'.$groupTemplate.'%')
                            ->get();
        }

        // Bobot Kriteria
        //BOBOT DIBIKIN BUAT MASING" KRITERIA
        if($pilihTipe == "highKarbo"){
            $W_karbo = 0.539;
            $W_protein = 0.297;
            $W_lemak = 0.164;
        }
        elseif($pilihTipe == "moderateKarbo"){
            $W_karbo = 0.297;
            $W_protein = 0.164;
            $W_lemak = 0.539;
        }
        elseif($pilihTipe == "lowKarbo"){
            $W_karbo = 0.142;
            $W_protein = 0.525;
            $W_lemak = 0.334;
        }
        
        // DATA RESEP
        $listResepIdentity = [];
        $alternatifResep = [];
        //Limiting Profile
        $limitProfile = $limitinProfileTipe[$pilihTipe];
        //Local Priorities List
        $appropriate = [];
        $in_appropriate = [];
        //List Menu Recommended
        $menuRecomen = [];

        foreach ($selectResep as $resepSatuan) {
            $idResep = $resepSatuan->id;
            $judulResep = $resepSatuan->judul_resep;
            $coverResep = $resepSatuan->cover;
            $healthLabelsResep = $resepSatuan->healthLabels;
            $totalTimeResep = $resepSatuan->totalTime;
            $cuisineTypeResep = $resepSatuan->cuisineType;
            $jumlahServingResep =  $resepSatuan->yield;
            $caloriesResep = ($resepSatuan->calories)/$jumlahServingResep;
            $scoreResep = $resepSatuan->score; // blm masuk
            $likesResep = $resepSatuan->likes;

            $fat = "";
            $protein = "";
            $carbs = "";
            foreach (json_decode ($resepSatuan->digest) as $macros){
                if($macros->label == "Fat"){
                   $fat = ($macros->total)/$jumlahServingResep;
                }
                elseif($macros->label == "Protein"){
                    $protein = ($macros->total)/$jumlahServingResep;
                }
                elseif($macros->label == "Carbs"){
                    $carbs = ($macros->total)/$jumlahServingResep;
                }
            }

            $resepKarakter = array(
                'idResep'=> $idResep,
                'karbo'=> $carbs,
                'lemak'=> $fat,
                'protein'=> $protein,
            );

            $resepIdentity = array(
                'idResep'=> $idResep,
                'judul_resep'=> $judulResep,
                'cover'=> $coverResep,
                'healthLabels'=> $healthLabelsResep,
                'calories'=> $caloriesResep,
                'totalWeight'=> $jumlahServingResep,
                'totalTime'=> $totalTimeResep,
                'cuisineTypeResep'=> $cuisineTypeResep,
                'likesResep'=> $likesResep,
                'macro' => $resepKarakter,
            );

             

            if($fat == 0){
                $fat = 1;
            }
            if($protein == 0){
                $protein = 1;
            }
            if($carbs == 0){
                $carbs = 1;
            }
            
            
            //////////
            //start cari local priorities alternatif // karbo , protein , lemak
            //matrix alternatif
            $a_m = array( 
                array(1,($limitProfile["karbo"]/$carbs),($limitProfile["lemak"]/$carbs)),
                array(($limitProfile["karbo"]/$protein),1,($limitProfile["lemak"]/$protein)),
                array(($limitProfile["karbo"]/$fat),($limitProfile["protein"]/$fat),1),
            );
            //total kolom
            $tk_a = array( 
                ($a_m[0][0]+$a_m[1][0]+$a_m[2][0]),
                ($a_m[0][1]+$a_m[1][1]+$a_m[2][1]),
                ($a_m[0][2]+$a_m[1][2]+$a_m[2][2])
            );
            //normal matrix alternatif
            $norm_a_m= array(
                array(($a_m[0][0]/$tk_a[0]),($a_m[0][1]/$tk_a[1]),($a_m[0][2]/$tk_a[2])),
                array(($a_m[1][0]/$tk_a[0]),($a_m[1][1]/$tk_a[1]),($a_m[1][2]/$tk_a[2])),
                array(($a_m[2][0]/$tk_a[0]),($a_m[2][1]/$tk_a[1]),($a_m[2][2]/$tk_a[2])),
            );
            //local priorties alternatif
            $lp_a = array( 
                ($norm_a_m[0][0]+$norm_a_m[0][1]+$norm_a_m[0][2])/3,
                ($norm_a_m[1][0]+$norm_a_m[1][1]+$norm_a_m[1][2])/3,
                ($norm_a_m[2][0]+$norm_a_m[2][1]+$norm_a_m[2][2])/3,
            );
            //end cari local priorities alternatif
            //////////
            

            //////////
            //start cari local priorities limit profile
            //matrix limit profile
            $lp_m = array( // karbo , protein , lemak
                array(1,$protein/($limitProfile["karbo"]),($fat/$limitProfile["karbo"])),
                array(($carbs/$limitProfile["protein"]),1,($fat/$limitProfile["protein"])),
                array(($carbs/$limitProfile["lemak"]),($protein/$limitProfile["lemak"]),1),
            );
            //total kolom
            $tk_lp = array( 
                ($lp_m[0][0]+$lp_m[1][0]+$lp_m[2][0]),
                ($lp_m[0][1]+$lp_m[1][1]+$lp_m[2][1]),
                ($lp_m[0][2]+$lp_m[1][2]+$lp_m[2][2])
            );
            //normal matrix limit profile
            $norm_lp_m= array(
                array(($lp_m[0][0]/$tk_lp[0]),($lp_m[0][1]/$tk_lp[1]),($lp_m[0][2]/$tk_lp[2])),
                array(($lp_m[1][0]/$tk_lp[0]),($lp_m[1][1]/$tk_lp[1]),($lp_m[1][2]/$tk_lp[2])),
                array(($lp_m[2][0]/$tk_lp[0]),($lp_m[2][1]/$tk_lp[1]),($lp_m[2][2]/$tk_lp[2])),
            );
            //local priorties limit profile
            $lp_lp = array( 
                ($norm_lp_m[0][0]+$norm_lp_m[0][1]+$norm_lp_m[0][2])/3,
                ($norm_lp_m[1][0]+$norm_lp_m[1][1]+$norm_lp_m[1][2])/3,
                ($norm_lp_m[2][0]+$norm_lp_m[2][1]+$norm_lp_m[2][2])/3,
            );
            //end cari local priorities limit profile
            //////////

            //Overall Scor
            $P_a = round(($lp_a[0]*$W_karbo) +($lp_a[1]*$W_protein) + ($lp_a[2]*$W_lemak),3);
            $P_lp = round(($lp_lp[0]*$W_karbo) +($lp_lp[1]*$W_protein) + ($lp_lp[2]*$W_lemak),3);

            // blm di sort berdasarkan nilai
            if($P_lp > $P_a){//appropriate
                array_push($appropriate,array('score_a'=>$P_a, 'score_lp'=>$P_lp, 'idResep'=>$idResep));
            }
            else{// inappropriate
                array_push($in_appropriate,$idResep);
            }
            
            array_push($listResepIdentity,$resepIdentity);
            array_push($alternatifResep,$resepKarakter);
        }
        
        ////PICK RANDOM 10
        //Membuat List Resep Dengan Data Complete
        foreach($appropriate as $theID){
            foreach($listResepIdentity as $resepComplete){
                if($theID['idResep'] == $resepComplete['idResep']){
                    array_push($menuRecomen, $resepComplete);
                } 
            }
        }
        
        //Randome Pick 10
        $jumlahRand = 10;
        if(count($menuRecomen)<10){
            $jumlahRand = count($menuRecomen);
        }
        // dapat Arraynya
        $rand_keys = array_rand($menuRecomen, $jumlahRand);
        $sampleMenu = [];
        foreach($rand_keys as $index){
            array_push($sampleMenu, $menuRecomen[$index]);
        }
        $menuRecomen = $sampleMenu;
        ////PICK RANDOM 10
        
        return $menuRecomen;
        // return dd($menuRecomen);
    }

    public function egosimilar(){
        ini_set('max_execution_time', 300000000);
        $acuan_labelHealth = array( "Sugar-Conscious" , "Keto-Friendly" , "Vegan" , "Vegetarian" , "Mediterranean" , "Dairy-Free" , "Gluten-Free" , "Egg-Free" , "Tree-Nut-Free" , "Fish-Free", "Pork-Free", "Red-Meat-Free" , "Crustacean-Free" , "Lupine-Free" , "Mollusk-Free" , "Alcohol-Free", "Paleo" , "DASH");

        $userId = Auth::id();

        $get_user_profil = DB::table('user_profile')
                        ->select("*")
                        ->where('idUser', $userId)
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();
        $tdee_user = $this->getTDEE();

        $batas_atas = $tdee_user['tdeeBersih']+$tdee_user['tdeeBersih']*(10/100);
        $batas_bawah = $tdee_user['tdeeBersih']-$tdee_user['tdeeBersih']*(10/100);
        

        $user_label_array = explode(",",$get_user_profil->label);

        $kx = count($user_label_array);

        $list_user_breakfast = [];
        $list_user_lunch = [];
        $list_user_dinner = [];

        $jumlahGenerate_self = DB::table('generate')->where('idUser',"=",$userId)->count();

        //list user dari survey
            foreach($user_label_array as $label){
            
                //cari user dengan interst sama di breakfast
                $select_survey_breakfast = DB::table("z_usersurvey")
                                            ->selectRaw('distinct z_usersurvey.id, z_usersurvey.gender, z_usersurvey.tinggi, z_usersurvey.berat,z_usersurvey.usia,z_usersurvey.aktivitas,z_usersurvey.kota,z_usersurvey.provinsi')
                                            ->join("z_surveybreakfast","z_surveybreakfast.id_User","=","z_usersurvey.id")
                                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                                            ->where("healthLabels","like","%".$label."%")
                                            ->where("dietPref","=",$get_user_profil->orientasi_makanan)
                                            ->where("kota","=",$get_user_profil->kota)
                                            ->get();

                foreach($select_survey_breakfast as $userinterest){
                    $tdee_userInterest = $this->getTDEE_other($userinterest->tinggi, $userinterest->berat,$userinterest->aktivitas,"maintain",$userinterest->gender,$userinterest->usia);
                    if($tdee_userInterest['tdeeBersih'] >= $batas_bawah && $tdee_userInterest['tdeeBersih'] <= $batas_atas){
                        if(!in_array($userinterest->id,$list_user_breakfast)){
                            array_push($list_user_breakfast, $userinterest->id);
                        }
                    }
                }


                //cari user dengan interst sama di lunch
                $select_survey_lunch = DB::table("z_usersurvey")
                                            ->selectRaw('distinct z_usersurvey.id, z_usersurvey.gender, z_usersurvey.tinggi, z_usersurvey.berat,z_usersurvey.usia,z_usersurvey.aktivitas,z_usersurvey.kota,z_usersurvey.provinsi')
                                            ->join("z_surveylunch","z_surveylunch.id_User","=","z_usersurvey.id")
                                            ->join("list_resep","list_resep.id","=","z_surveylunch.id_Resep")
                                            ->where("healthLabels","like","%".$label."%")
                                            ->where("dietPref","=",$get_user_profil->orientasi_makanan)
                                            ->where("kota","=",$get_user_profil->kota)
                                            ->get();

                foreach($select_survey_lunch as $userinterest){
                    $tdee_userInterest = $this->getTDEE_other($userinterest->tinggi, $userinterest->berat,$userinterest->aktivitas,"maintain",$userinterest->gender,$userinterest->usia);
                    if($tdee_userInterest['tdeeBersih'] >= $batas_bawah && $tdee_userInterest['tdeeBersih'] <= $batas_atas){
                        if(!in_array($userinterest->id,$list_user_lunch)){
                            array_push($list_user_lunch, $userinterest->id);
                        }
                    }
                }

                //cari user dengan interst sama di dinner
                $select_survey_dinner = DB::table("z_usersurvey")
                                            ->selectRaw('distinct z_usersurvey.id, z_usersurvey.gender, z_usersurvey.tinggi, z_usersurvey.berat,z_usersurvey.usia,z_usersurvey.aktivitas,z_usersurvey.kota,z_usersurvey.provinsi')
                                            ->join("z_surveydinner","z_surveydinner.id_User","=","z_usersurvey.id")
                                            ->join("list_resep","list_resep.id","=","z_surveydinner.id_Resep")
                                            ->where("healthLabels","like","%".$label."%")
                                            ->where("dietPref","=",$get_user_profil->orientasi_makanan)
                                            ->where("kota","=",$get_user_profil->kota)
                                            ->get();

                foreach($select_survey_dinner as $userinterest){
                    $tdee_userInterest = $this->getTDEE_other($userinterest->tinggi, $userinterest->berat,$userinterest->aktivitas,"maintain",$userinterest->gender,$userinterest->usia);
                    if($tdee_userInterest['tdeeBersih'] >= $batas_bawah && $tdee_userInterest['tdeeBersih'] <= $batas_atas){
                        if(!in_array($userinterest->id,$list_user_dinner)){
                            array_push($list_user_dinner, $userinterest->id);
                        }
                    }
                }

            }

            $list_user = array_unique (array_merge ($list_user_breakfast, $list_user_lunch,$list_user_dinner));

            //koefisien
            $w1 = 0.25;
            $w2 = 0.75;
            $w_change = 0.3;

            $sample_breakfast_label=[];
            $final_sample_breakfast_label=[];

            $sample_lunch_label=[];
            $final_sample_lunch_label=[];

            $sample_dinner_label=[];
            $final_sample_dinner_label=[];

            foreach($user_label_array as $label){
                foreach($list_user as $userID){
                    
                    //Breakfast
                        $select_menu_breakfast_max_urut = DB::table("z_surveybreakfast")
                                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                                            ->where("id_User","=",$userID)
                                            ->max('Urutan');

                        $select_menu_breakfast_max_urut++;

                        $select_menu_breakfast = DB::table("z_surveybreakfast")
                                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                                            ->where("id_User","=",$userID)
                                            ->where("healthLabels","like","%".$label."%")
                                            ->get();
                        
                        //cari rata-rata ratings
                        $healt_label_collect = [];
                        foreach($select_menu_breakfast as $usser_current_menu){
                            $array_menu_label = explode(",",str_replace(array("[",'"',"]"),"",$usser_current_menu->healthLabels));
                            foreach($array_menu_label as $label_menu_current){
                                if(in_array($label_menu_current,$acuan_labelHealth)){
                                    if(array_key_exists($label_menu_current, $healt_label_collect)){
                                        $healt_label_collect[$label_menu_current]['urutan'] += $usser_current_menu->Urutan;
                                        $healt_label_collect[$label_menu_current]['jumlah_loop']++;
                                    }
                                    else{
                                        $temp = array("jumlah_loop"=>1,"urutan"=>($select_menu_breakfast_max_urut - $usser_current_menu->Urutan));
                                        $healt_label_collect[$label_menu_current] = $temp;
                                    }
                                }
                            }
                        }
                        //cari rata-rata tiap label
                        foreach($healt_label_collect as $label => $val){
                            $healt_label_collect[$label] = $val['urutan']/$val['jumlah_loop'];
                        }

                        foreach($healt_label_collect as $label=>$val){
                            if(array_key_exists($label  , $sample_breakfast_label)){
                                $temp =$sample_breakfast_label[$label];
                                array_push($temp,$val);
                                $sample_breakfast_label[$label] = $temp;
                            }
                            else{
                                $sample_breakfast_label[$label] = array($val);
                            }
                        }
                        
                    //Breakfast
                    

                    //Lunch
                        $select_menu_lunch_max_urut = DB::table("z_surveylunch")
                                            ->join("list_resep","list_resep.id","=","z_surveylunch.id_Resep")
                                            ->where("id_User","=",$userID)
                                            ->max('Urutan');

                        $select_menu_lunch_max_urut++;

                        $select_menu_lunch = DB::table("z_surveylunch")
                                            ->join("list_resep","list_resep.id","=","z_surveylunch.id_Resep")
                                            ->where("id_User","=",$userID)
                                            ->where("healthLabels","like","%".$label."%")
                                            ->get();

                        //cari rata-rata ratings
                        $healt_label_collect = [];
                        foreach($select_menu_lunch as $usser_current_menu){
                            $array_menu_label = explode(",",str_replace(array("[",'"',"]"),"",$usser_current_menu->healthLabels));
                            foreach($array_menu_label as $label_menu_current){
                                if(in_array($label_menu_current,$acuan_labelHealth)){
                                    if(array_key_exists($label_menu_current, $healt_label_collect)){
                                        $healt_label_collect[$label_menu_current]['urutan'] += $usser_current_menu->Urutan;
                                        $healt_label_collect[$label_menu_current]['jumlah_loop']++;
                                    }
                                    else{
                                        $temp = array("jumlah_loop"=>1,"urutan"=>($select_menu_lunch_max_urut - $usser_current_menu->Urutan));
                                        $healt_label_collect[$label_menu_current] = $temp;
                                    }
                                }
                            }
                        }
                        //cari rata-rata tiap label
                        foreach($healt_label_collect as $label => $val){
                            $healt_label_collect[$label] = $val['urutan']/$val['jumlah_loop'];
                        }

                        foreach($healt_label_collect as $label=>$val){
                            if(array_key_exists($label  , $sample_lunch_label)){
                                $temp =$sample_lunch_label[$label];
                                array_push($temp,$val);
                                $sample_lunch_label[$label] = $temp;
                            }
                            else{
                                $sample_lunch_label[$label] = array($val);
                            }
                        }
                    //Lunch

                    //Dinner
                        $select_menu_dinner_max_urut = DB::table("z_surveydinner")
                        ->join("list_resep","list_resep.id","=","z_surveydinner.id_Resep")
                        ->where("id_User","=",$userID)
                        ->max('Urutan');

                        $select_menu_dinner_max_urut++;

                        $select_menu_dinner = DB::table("z_surveydinner")
                                            ->join("list_resep","list_resep.id","=","z_surveydinner.id_Resep")
                                            ->where("id_User","=",$userID)
                                            ->where("healthLabels","like","%".$label."%")
                                            ->get();

                        //cari rata-rata ratings
                        $healt_label_collect = [];
                        foreach($select_menu_dinner as $usser_current_menu){
                            $array_menu_label = explode(",",str_replace(array("[",'"',"]"),"",$usser_current_menu->healthLabels));
                            foreach($array_menu_label as $label_menu_current){
                                if(in_array($label_menu_current,$acuan_labelHealth)){
                                    if(array_key_exists($label_menu_current, $healt_label_collect)){
                                        $healt_label_collect[$label_menu_current]['urutan'] += $usser_current_menu->Urutan;
                                        $healt_label_collect[$label_menu_current]['jumlah_loop']++;
                                    }
                                    else{
                                        $temp = array("jumlah_loop"=>1,"urutan"=>($select_menu_dinner_max_urut - $usser_current_menu->Urutan));
                                        $healt_label_collect[$label_menu_current] = $temp;
                                    }
                                }
                            }
                        }
                        //cari rata-rata tiap label
                        foreach($healt_label_collect as $label => $val){
                            $healt_label_collect[$label] = $val['urutan']/$val['jumlah_loop'];
                        }

                        foreach($healt_label_collect as $label=>$val){
                            if(array_key_exists($label  , $sample_dinner_label)){
                                $temp =$sample_dinner_label[$label];
                                array_push($temp,$val);
                                $sample_dinner_label[$label] = $temp;
                            }
                            else{
                                $sample_dinner_label[$label] = array($val);
                            }
                        }
                    //Dinner
                }

                //calculate final overall rating
                    foreach($sample_breakfast_label as $labels => $array_nom){
                        $jumlahPush = count($array_nom);

                        $totalAngka = 0;
                        foreach($array_nom as $angka){
                            $totalAngka += $angka;
                        }
                        $final_sample_breakfast_label[$labels] = $totalAngka/$jumlahPush;
                    }

                    foreach($sample_lunch_label as $labels => $array_nom){
                        $jumlahPush = count($array_nom);

                        $totalAngka = 0;
                        foreach($array_nom as $angka){
                            $totalAngka += $angka;
                        }
                        $final_sample_lunch_label[$labels] = $totalAngka/$jumlahPush;
                    }

                    foreach($sample_dinner_label as $labels => $array_nom){
                        $jumlahPush = count($array_nom);

                        $totalAngka = 0;
                        foreach($array_nom as $angka){
                            $totalAngka += $angka;
                        }
                        $final_sample_dinner_label[$labels] = $totalAngka/$jumlahPush;
                    }
                //calculate final overall rating  
            }


            $get_all_user_survey = DB::table("z_surveybreakfast")
                        ->selectRaw("DISTINCT z_surveybreakfast.id_User")
                        ->join("z_usersurvey","z_usersurvey.id","=","z_surveybreakfast.id_User")
                        ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                        ->where("healthLabels","like","%".$label."%")
                        ->where("dietPref","=",$get_user_profil->orientasi_makanan)
                        ->get();
                        
            //cari matching
            $matching = [];
            foreach($get_all_user_survey as $user){
                //k loop
                foreach($final_sample_breakfast_label as $label => $rating){
                    $get_breakfast_data = DB::table("z_surveybreakfast")
                                            ->selectRaw('healthLabels, Urutan, list_resep.id')
                                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                                            ->where("id_User","=",$user->id_User)->get();
                    $get_breakfast_data_max = DB::table("z_surveybreakfast")
                                            ->selectRaw('healthLabels, Urutan')
                                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                                            ->where("id_User","=",$user->id_User)->max("Urutan");

                    //per makanan

                    $total_kanan = 0;
                    $total_kiri = 0;
                    foreach($get_breakfast_data as $dataMakanan){
                        $idResep = $dataMakanan->id;
                        $array_menu_label = explode(",",str_replace(array("[",'"',"]"),"",$dataMakanan->healthLabels));

                        $total_kiri += abs($rating -($get_breakfast_data_max - $dataMakanan->Urutan));
                        
                        //makanan yang ada di acuan label dan ada di label profil
                        foreach($array_menu_label as $label){
                            if(in_array($label,$acuan_labelHealth) && array_key_exists($label, $final_sample_breakfast_label)){
                                $m = DB::table("z_surveybreakfast")
                                        ->where("id_Resep","=",$idResep)->count();
                                $n = (DB::table("z_surveybreakfast")
                                        ->selectRaw("count(DISTINCT( id_User)) as 'banyak_orang'")
                                        ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                                        ->where("healthLabels","like","%".$label."%")
                                        ->first())->banyak_orang;
                                
                                $total_kanan = $total_kanan + $this->get_d2(($m/$n),$w_change,$idResep,$user->id_User,$get_breakfast_data_max,$final_sample_breakfast_label,$label,$list_user);
                            }
                        }
                    }

                    $kiri = ($w1*$total_kiri);

                    $kanan = ($w2/count($get_breakfast_data))*$total_kanan;
                    
                    $matching[$user->id_User] = (1/$kx)*($kiri+$kanan);
                }
            }
            dd($matching);
        //list user dari survey
            
            

    }

    public function get_d2($wci,$W_change,$idResep,$id_user_y,$max_y,$final_sample_breakfast_label,$label,$array_user){


        $dataResep = DB::table('z_surveybreakfast')
                    ->where('id_Resep','=',$idResep)
                    ->where('id_User','=',$id_user_y)
                    ->first();

        $rata_umum = $final_sample_breakfast_label[$label];


        //array_pengguna
            $cari_sd_pengguna = DB::table('z_surveybreakfast')
                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                            ->where('id_User','=',$id_user_y)
                            ->get();

            $cari_sd_pengguna_max = DB::table('z_surveybreakfast')
                            ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                            ->where('id_User','=',$id_user_y)
                            ->max("Urutan");
            $cari_sd_pengguna_max++;

            $ratingArray = [];

            foreach($cari_sd_pengguna as $user){
                array_push($ratingArray,($cari_sd_pengguna_max - $user->Urutan));
            }
        //array_pengguna

        //array_item
            $cari_sd_item = DB::table('z_surveybreakfast')
                    ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                    ->where("healthLabels","like","%".$label."%")
                    ->get();

            $cari_sd_item_max = DB::table('z_surveybreakfast')
                    ->join("list_resep","list_resep.id","=","z_surveybreakfast.id_Resep")
                    ->where("healthLabels","like","%".$label."%")
                    ->max("Urutan");
            $cari_sd_item_max++;

            $ratingArray_item = [];

            foreach($cari_sd_item as $user){
                array_push($ratingArray_item,($cari_sd_item_max - $user->Urutan));
            }
        //array_item

        $rating = abs($this->bias($rata_umum,$ratingArray,$ratingArray_item));

        return(1-(0.11*$rating));
         
    }

    public function bias($rata_umum,$array_pengguna,$array_item){
        $deviasi_pengguna = $this->Stand_Deviation($array_pengguna);
        $deviasi_item = $this->Stand_Deviation($array_item);

        return ($rata_umum+$deviasi_pengguna+$deviasi_item);
    }

    public function Stand_Deviation($arr){
        $num_of_elements = count($arr);
          
        $variance = 0.0;
          
                // calculating mean using array_sum() method
        $average = array_sum($arr)/$num_of_elements;
          
        foreach($arr as $i)
        {
            // sum of squares of differences between 
                        // all numbers and means.
            $variance += pow(($i - $average), 2);
        }
          
        return (float)sqrt($variance/$num_of_elements);
    }

}
