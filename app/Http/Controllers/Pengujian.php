<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Pengujian extends Controller
{
    public function uji_ego1(){
        ini_set('max_execution_time', 300000000);


        $select_userSuvey = DB::table('z_usersurvey')
                        ->select("*")
                        ->get();


        echo '  <table>
                    <thead>
                        <tr align="center">
                            <th>No</th>
                            <th>TDEE</th>
                            <th>Batas Bawah</th>
                            <th>Batas Atas</th>
                            <th>jumlahPairingAwal</th>
                            <th>jumlahMemenuhiKriteria</th>
                            <th>Total Kalori</th>
                        </tr>
                    </thead>
                
                    <tbody>';

                    
        $co = 1;

        foreach($select_userSuvey as $user){
            $list_breakfast = DB::table('z_surveybreakfast')
                            ->select("list_resep.id as idResep","list_resep.healthLabels", "z_surveybreakfast.Urutan", "z_surveybreakfast.Frekunsi")
                            ->join('list_resep', 'z_surveybreakfast.id_Resep', '=', 'list_resep.id')
                            ->where("id_user","=",$user->id)
                            ->where("Frekunsi","!=","0")
                            ->get();
            $list_lunch = DB::table('z_surveylunch')
                            ->select("list_resep.id as idResep","list_resep.healthLabels", "z_surveylunch.Urutan", "z_surveylunch.Frekunsi")
                            ->join('list_resep', 'z_surveylunch.id_Resep', '=', 'list_resep.id')
                            ->where("id_user","=",$user->id)
                            ->where("Frekunsi","!=","0")
                            ->get();
            $list_dinner = DB::table('z_surveydinner')
                            ->select("list_resep.id as idResep","list_resep.healthLabels", "z_surveydinner.Urutan", "z_surveydinner.Frekunsi")
                            ->join('list_resep', 'z_surveydinner.id_Resep', '=', 'list_resep.id')
                            ->where("id_user","=",$user->id)
                            ->where("Frekunsi","!=","0")
                            ->get();
            $gen = $this->egosimilar_v1($user->id,$user->tinggi,$user->berat,$user->aktivitas,"maintain",$user->gender,$user->usia,$list_breakfast,$list_lunch,$list_dinner);
        }
        

        
        echo"</tbody>
        </table>";
    }
    public function egosimilar_v1($idUser,$tb, $bb, $aktifitas, $goal, $gender,$usia,$list_breakfast,$list_lunch,$list_dinner){ //pure dengan parameter TDEE BMR
        $nutrisi_param = $this -> getTDEE($tb, $bb, $aktifitas, $goal, $gender,$usia);

        //check user responden survey selain user x (untuk list user y)
        $select_userSuvey = DB::table('z_usersurvey')
                        ->select("*")
                        ->where("id","!=",$idUser)
                        ->get();

        //get rating summary (hasil survey) dari user x
        $user_x_ratingSummary = $this -> getSummary_Rating_Survey($list_breakfast,$list_lunch,$list_dinner);
        //menu pilihan x (hasil survey) di kumpulkan
        $pilihan_x = array("breakfast"=>$list_breakfast,"lunch"=>$list_lunch,"dinner"=>$list_dinner);
        
        //Cari % matching
        foreach($select_userSuvey as $userSurvey){ // tiap user hasil survey
            $user_tdee = $this->getTDEE($userSurvey->tinggi, $userSurvey->berat, $userSurvey->aktivitas, "maintain",  $userSurvey->gender,$userSurvey->usia);
            $list_y_breakfast = DB::table('z_surveybreakfast')
                        ->select("list_resep.id as idResep","list_resep.healthLabels", "z_surveybreakfast.Urutan", "z_surveybreakfast.Frekunsi")
                        ->join('list_resep', 'z_surveybreakfast.id_Resep', '=', 'list_resep.id')
                        ->where("id_user","=",$userSurvey->id)
                        ->where("Frekunsi","!=","0")
                        ->get();
            $list_y_lunch = DB::table('z_surveylunch')
                        ->select("list_resep.id as idResep","list_resep.healthLabels", "z_surveylunch.Urutan", "z_surveylunch.Frekunsi")
                        ->join('list_resep', 'z_surveylunch.id_Resep', '=', 'list_resep.id')
                        ->where("id_user","=",$userSurvey->id)
                        ->where("Frekunsi","!=","0")
                        ->get();
            $list_y_dinner = DB::table('z_surveydinner')
                        ->select("list_resep.id as idResep","list_resep.healthLabels", "z_surveydinner.Urutan", "z_surveydinner.Frekunsi")
                        ->join('list_resep', 'z_surveydinner.id_Resep', '=', 'list_resep.id')
                        ->where("id_user","=",$userSurvey->id)
                        ->where("Frekunsi","!=","0")
                        ->get();

            //menu pilihan y (hasil survey) di kumpulkan
            $pilihan_y = array("breakfast"=>$list_y_breakfast,"lunch"=>$list_y_lunch,"dinner"=>$list_y_dinner);
            //get rating summary (hasil survey) dari user y
            $user_y_ratingSummary = $this -> getSummary_Rating_Survey($list_y_breakfast,$list_y_lunch,$list_y_dinner);
            //hitung matching
            $hasil_egosimilar = $this->get_egosimilar_plus($idUser,$user_x_ratingSummary, $user_y_ratingSummary,$pilihan_x , $pilihan_y);

            $dataMatching = array('idUser_y'=>$userSurvey->id);
            dd($user_y_ratingSummary);
        }


        dd($select_userSuvey);
    }
    

    public function get_egosimilar_plus($idUserx,$user_x, $user_y, $pilihan_x,$pilihan_y){
        //Data summary rating tiap template di define satu-satu
        $matching_breakfast_x = $user_x['breakfast'];
        $matching_lunch_x = $user_x['lunch'];
        $matching_dinner_x = $user_x['dinner'];

        $matching_breakfast_y = $user_y['breakfast'];
        $matching_lunch_y = $user_y['lunch'];
        $matching_dinner_y = $user_y['dinner'];

        //Dihutng jumlah K (banyak data)
        $kx_breakfast = count($matching_breakfast_x);
        $kx_lunch = count($matching_lunch_x);
        $kx_dinner = count($matching_dinner_x);

        //koefisien
        $w1 = 0.25;
        $w2 = 0.75;
        $w_change = 0.3;

        //hitung nilai breakfast
        $hitung_breakfast = 0;
        foreach($matching_breakfast_x as $interest=>$val){ //loop tiap interest di tempate menu

            //cari sisi kiri
            if(isset($matching_breakfast_y[$interest])){ // kalok y ada interest nya juga
                $sisi_kiri = $w1 *(1-(0.11*$this->d1_absolute_diff($val['mean'],$matching_breakfast_y[$interest]['mean'])));
            }
            else{// kalo y gk ada interest
                $sisi_kiri = $w1 * 0;
            }

            //cari sisi kanan (per item yang pernah di generate)
            $temp_sisi_kanan =  0;
                //id resep item y di masukan jadi 1 array
                $idResep_Y = [];    
                foreach($pilihan_y['breakfast'] as $itemY){
                    array_push($idResep_Y,$itemY->idResep);
                }
                //hitung sisi kanan per item x simpan
            $jumlahItem_ncx = DB::table('z_surveybreakfast')
                            ->select("*")
                            ->join('list_resep', 'list_resep.id', '=', 'z_surveybreakfast.id_Resep')
                            ->where('healthLabels',"like","%".$interest."%")
                            ->where('id_User',"=",$idUserx)
                            ->get();
            
            $rata_rata = 0;
            foreach($jumlahItem_ncx as $item_x_cth){ // loop per item x
                //check apakah y ada item yang sama
                if(in_array($item_x_cth->id_Resep,$idResep_Y)){//kalau ada
                    $nilai_maks_x = $this->cari_nilai_maks($jumlahItem_ncx);
                    $nilai_maks_y = $this->cari_nilai_maks($pilihan_y['breakfast']);

                    //cari urutan y
                    $urutanY = $this->get_urutanSurvey_dr_list($pilihan_y['breakfast'],$item_x_cth->id_Resep);
                    
                    //cari rating sesungguhnya
                    $nilai_rating_x = $nilai_maks_x - $item_x_cth->Urutan;
                    $nilai_rating_y = $nilai_maks_y - $urutanY;

                    //cari d2
                    $temp_d2 = abs($nilai_rating_x-$nilai_rating_y);
                    $rata_rata += abs($nilai_rating_x-$nilai_rating_y);
                    echo $rata_rata."<br>";
                    $temp_sisi_kanan = $temp_sisi_kanan+$this->d2_per_item($item_x_cth->id_Resep,"breakfast",$interest, $temp_d2, $w_change);
                }
                else{//kalau gk ada
                    $temp_sisi_kanan = $temp_sisi_kanan+0;
                }
            }

            dd($rata_rata);

            $ncx = count($jumlahItem_ncx);

            $sisi_kanan = ($w2/$ncx)*$temp_sisi_kanan;

            dd($this->get_rata_bias($interest));

            // dd($sisi_kiri);
        }

        echo (1/$kx_breakfast)*$sisi_kiri+$sisi_kanan."<br>";

        

        dd("hitung_breakfast");

        // $matching_result_breakfast = (1/$kx_breakfast)*();

        
    }

    public function get_rata_bias($interest){
        $total_breakfast = 0;
        $MAX_GENERATE_BREKFAST = DB::table('z_surveybreakfast')
                                ->join('list_resep', 'list_resep.id', '=', 'z_surveybreakfast.id_Resep')
                                ->where('healthLabels',"like","%".$interest."%")->max('Urutan');
        $ALL_GENERATE_BREKFAST = DB::table('z_surveybreakfast')
                                ->select('Urutan')
                                ->join('list_resep', 'list_resep.id', '=', 'z_surveybreakfast.id_Resep')
                                ->where('healthLabels',"like","%".$interest."%")->get();

        foreach($ALL_GENERATE_BREKFAST as $row){
            $total_breakfast += $MAX_GENERATE_BREKFAST - $row->Urutan;
        }

        $mean_breakfast = $total_breakfast/count($ALL_GENERATE_BREKFAST);



        $total_lunch = 0;
        $MAX_GENERATE_LUNCH = DB::table('z_surveylunch')
                                ->join('list_resep', 'list_resep.id', '=', 'z_surveylunch.id_Resep')
                                ->where('healthLabels',"like","%".$interest."%")->max('Urutan');

        $ALL_GENERATE_LUNCH = DB::table('z_surveylunch')
                                ->select('Urutan')
                                ->join('list_resep', 'list_resep.id', '=', 'z_surveylunch.id_Resep')
                                ->where('healthLabels',"like","%".$interest."%")->get();

        foreach($ALL_GENERATE_LUNCH as $row){
            $total_lunch += $MAX_GENERATE_LUNCH - $row->Urutan;
        }

        $mean_lunch = $total_lunch/count($ALL_GENERATE_LUNCH);


        $total_dinner = 0;
        $MAX_GENERATE_DINNER = DB::table('z_surveydinner')
                                ->join('list_resep', 'list_resep.id', '=', 'z_surveydinner.id_Resep')
                                ->where('healthLabels',"like","%".$interest."%")->max('Urutan');
        $ALL_GENERATE_DINNER = DB::table('z_surveydinner')
                                ->select('Urutan')
                                ->join('list_resep', 'list_resep.id', '=', 'z_surveydinner.id_Resep')
                                ->where('healthLabels',"like","%".$interest."%")->get();

        foreach($ALL_GENERATE_DINNER as $row){
            $total_dinner += $MAX_GENERATE_DINNER - $row->Urutan;
        }

        return ($total_breakfast+$total_lunch+$total_dinner)/3;
    }

    public function egoSimilar_bias($rata_rata,$deviasi_user,$deviasi_item){

    }

    public function get_urutanSurvey_dr_list($list_menu, $idCari){

        foreach($list_menu as $list_item){
            if($list_item->idResep == $idCari){
                return $list_item->Urutan;
            }
        }

    }

    public function cari_nilai_maks($list_menu){
            $maks = 0;
            foreach($list_menu as $labels){
                if($maks < $labels->Urutan){
                    $maks = $labels->Urutan;
                }
            }
            $maks ++;
        return $maks;
    }

    public function getSummary_Rating_Survey($list_breakfast,$list_lunch,$list_dinner){
        $acuan_labelHealth = array( "Sugar-Conscious" , "Keto-Friendly" , "Vegan" , "Vegetarian" , "Mediterranean" , "Dairy-Free" , "Gluten-Free" , "Egg-Free" , "Tree-Nut-Free" , "Fish-Free", "Pork-Free", "Red-Meat-Free" , "Crustacean-Free" , "Lupine-Free" , "Mollusk-Free" , "Alcohol-Free", "Paleo" , "DASH");

        $breakfast_interest_x = array( "Sugar-Conscious" => array(),"Keto-Friendly" => array(),"Vegan" => array(),"Vegetarian" => array(),"Mediterranean" => array(),"Dairy-Free" => array(),"Gluten-Free" => array(),"Egg-Free" => array(),"Tree-Nut-Free" => array(),"Fish-Free"=> array(),"Pork-Free"=> array(),"Red-Meat-Free" => array(),"Crustacean-Free" => array(),"Lupine-Free" => array(),"Mollusk-Free" => array(),"Alcohol-Free"=> array(),"Paleo" => array(),"DASH"  => array());
        $lunch_interest_x = array( "Sugar-Conscious" => array(),"Keto-Friendly" => array(),"Vegan" => array(),"Vegetarian" => array(),"Mediterranean" => array(),"Dairy-Free" => array(),"Gluten-Free" => array(),"Egg-Free" => array(),"Tree-Nut-Free" => array(),"Fish-Free"=> array(),"Pork-Free"=> array(),"Red-Meat-Free" => array(),"Crustacean-Free" => array(),"Lupine-Free" => array(),"Mollusk-Free" => array(),"Alcohol-Free"=> array(),"Paleo" => array(),"DASH"  => array());
        $dinner_interest_x = array( "Sugar-Conscious" => array(),"Keto-Friendly" => array(),"Vegan" => array(),"Vegetarian" => array(),"Mediterranean" => array(),"Dairy-Free" => array(),"Gluten-Free" => array(),"Egg-Free" => array(),"Tree-Nut-Free" => array(),"Fish-Free"=> array(),"Pork-Free"=> array(),"Red-Meat-Free" => array(),"Crustacean-Free" => array(),"Lupine-Free" => array(),"Mollusk-Free" => array(),"Alcohol-Free"=> array(),"Paleo" => array(),"DASH"  => array());

        ////Hitung Rating Interest Breakfast pengguna X
            $maks = 0;
            //cari prioritasnya
            foreach($list_breakfast as $labels){
                $arr_healt_breakfast = explode(",",str_replace(array("[",'"',"]"),"",$labels->healthLabels));
                foreach($arr_healt_breakfast as $labelHealt){
                    if(in_array($labelHealt,$acuan_labelHealth)){
                        $tempArr = $breakfast_interest_x[$labelHealt];
                        array_push($tempArr,$labels->Urutan);
                        $breakfast_interest_x[$labelHealt] = $tempArr;
                        if($maks < $labels->Urutan){
                            $maks = $labels->Urutan;
                        }
                    }
                    
                }
            }
            $maks ++;
            foreach($breakfast_interest_x as $labels => $val){
                if(count($val)>0){
                    $banyak = 1;
                    $temp_cari = 0;
                    foreach($val as $uruts){
                        $temp_cari = $temp_cari + ($maks - $uruts);
                        $banyak++;
                    }
                    $breakfast_interest_x[$labels] = array("banyakMuncul" => $banyak, "mean" =>  ( $temp_cari/$banyak), "total"=> $temp_cari);
                }
                else{
                    unset($breakfast_interest_x[$labels]);
                }
            }
        ////Hitung Rating Interest Breakfast pengguna X


        ////Hitung Rating Interest Lunch pengguna X
            $maks = 0;
            //cari prioritasnya
            foreach($list_lunch as $labels){
                $arr_healt_breakfast = explode(",",str_replace(array("[",'"',"]"),"",$labels->healthLabels));
                foreach($arr_healt_breakfast as $labelHealt){
                    if(in_array($labelHealt,$acuan_labelHealth)){
                        $tempArr = $lunch_interest_x[$labelHealt];
                        array_push($tempArr,$labels->Urutan);
                        $lunch_interest_x[$labelHealt] = $tempArr;
                        if($maks < $labels->Urutan){
                            $maks = $labels->Urutan;
                        }
                    }
                    
                }
            }
            $maks ++;
            foreach($lunch_interest_x as $labels => $val){
                if(count($val)>0){
                    $banyak = 1;
                    $temp_cari = 0;
                    foreach($val as $uruts){
                        $temp_cari = $temp_cari + ($maks - $uruts);
                        $banyak++;
                    }
                    $lunch_interest_x[$labels] = array("banyakMuncul" => $banyak, "mean" =>  ( $temp_cari/$banyak), "total"=> $temp_cari);
                }
                else{
                    unset($lunch_interest_x[$labels]);
                }
            }
        ////Hitung Rating Interest Lunch pengguna X

        ////Hitung Rating Interest Dinner pengguna X
            $maks = 0;
            //cari prioritasnya
            foreach($list_dinner as $labels){
                $arr_healt_dinner = explode(",",str_replace(array("[",'"',"]"),"",$labels->healthLabels));
                foreach($arr_healt_dinner as $labelHealt){
                    if(in_array($labelHealt,$acuan_labelHealth)){
                        $tempArr = $dinner_interest_x[$labelHealt];
                        array_push($tempArr,$labels->Urutan);
                        $dinner_interest_x[$labelHealt] = $tempArr;
                        if($maks < $labels->Urutan){
                            $maks = $labels->Urutan;
                        }
                    }
                    
                }
            }
            $maks ++;
            foreach($dinner_interest_x as $labels => $val){
                if(count($val)>0){
                    $banyak = 1;
                    $temp_cari = 0;
                    foreach($val as $uruts){
                        $temp_cari = $temp_cari + ($maks - $uruts);
                        $banyak++;
                    }
                    $dinner_interest_x[$labels] = array("banyakMuncul" => $banyak, "mean" =>  ( $temp_cari/$banyak), "total"=> $temp_cari);
                }
                else{
                    unset($dinner_interest_x[$labels]);
                }
            }
        ////Hitung Rating Interest Dinner pengguna X

        return array(
            "breakfast" => $breakfast_interest_x,
            "lunch" => $lunch_interest_x,
            "dinner" => $dinner_interest_x
        );
    }

    public function d2_per_item($idResep_item_x,$template,$interest, $temp_d2, $w_change){
        $m = 0;
        $n = 0;

        $W_cari = 0;

        $d2 =  0;


        //cari jumlah pengguna yang masukin item itu
        if($template == "breakfast"){
            $m = DB::table('z_surveybreakfast')->select("*")->where('id_Resep',"=",$idResep_item_x)->distinct('id_User')->count();
            $n = DB::table('z_surveybreakfast')->select("*")->join('list_resep', 'list_resep.id', '=', 'z_surveybreakfast.id_Resep')->where('healthLabels',"like","%".$interest."%")->distinct('id_User')->count();
            $W_cari = $m/$n;
        }

        //Wci(x) dibulatkan ke 2 desimal
        $W_cari = (round($W_cari,2));

        //d2 adaptasi dengan popularitas item
        if($W_cari > 0.5 && $temp_d2 < 5){
            return $temp_d2 + ($w_change * $temp_d2);
        }
        elseif($W_cari > 0.5 && $temp_d2 >= 5){
            return $temp_d2 ;
        }
        elseif($W_cari <= 0.5 && $temp_d2 < 5){
            return $temp_d2 - ($w_change * $temp_d2);
        }
        elseif($W_cari <= 0.5 && $temp_d2 >= 5){
            return $temp_d2 + ($w_change * $temp_d2);
        }
    }

    public function d1_absolute_diff($ratingX, $ratingY){
        return abs($ratingX-$ratingY);
    }

    public function ujiAHP_2(){
        ini_set('max_execution_time', 300000000);
        $umur = [17,21,25,26,30,35,36,40,45];
        $goals = ["maintain","cutting","bulking"];
        // $pref = ["highKarbo","moderateKarbo","lowKarbo"];
        $orientasiMakan = ["normal","vegetarian","vegan"];
        $aktivitas = [1,2,3,4,5];
        $beratCowokRemaja = [49,50,70,71,80,81];
        $beratCowokDewasa = [52,53,70,71,84,85];

        $beratCewekRemaja = [43,44,64,65,76,77];
        $beratCewekDewasa = [46,47,63,64,75,76];

        // echo "<h1>Pria</h1>";
        echo '  <table>
                    <thead>
                        <tr align="center">
                            <th>No</th>
                            <th>TDEE</th>
                            <th>Batas Bawah</th>
                            <th>Batas Atas</th>
                            <th>jumlahPairingAwal</th>
                            <th>jumlahMemenuhiKriteria</th>
                            <th>Total Kalori</th>
                        </tr>
                    </thead>
                
                    <tbody>';
        $co = 1;
        foreach($umur as $usia){
            foreach($goals as $gl){
                foreach($aktivitas as $ak){
                    foreach($orientasiMakan as $om){
                        if($usia == 17){
                            foreach($beratCowokRemaja as $berat){
                                $gen = $this->generate_SET("lowKarbo",168,$berat,$ak,$gl,"M",$usia,$om);
                                // dd($gen);
                                if($gen['rekomen_final']  != null){
                                    foreach($gen['rekomen_final'] as $key => $kalori_hasil){
                                        echo "
                                            <tr>
                                                <td>".$co."</td>
                                                <td>".round($gen['tdee'],0)."</td>
                                                <td>".round($gen['batasBawah_TDEE'],0)."</td>
                                                <td>".round($gen['batasAtas_TDEE'],0)."</td>
                                                <td>".$gen['jumlahPairingAwal']."</td>
                                                <td>".$gen['jumlahMemenuhiKriteria']."</td>
                                                <td>".round($kalori_hasil,2)."</td>
                                            </tr>
                                        ";
                                    }
                                }
                                else{
                                    echo "
                                        <tr>
                                            <td>".$co."</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                        </tr>
                                    ";
                                }
                                $co++;
                            }
                        }
                        else{
                            foreach($beratCowokDewasa as $berat){
                                $gen = $this->generate_SET("lowKarbo",168,$berat,$ak,$gl,"M",$usia,$om);
                                if($gen['rekomen_final']  != null){
                                    foreach($gen['rekomen_final'] as $key => $kalori_hasil){
                                        echo "
                                            <tr>
                                                <td>".$co."</td>
                                                <td>".round($gen['tdee'],0)."</td>
                                                <td>".round($gen['batasBawah_TDEE'],0)."</td>
                                                <td>".round($gen['batasAtas_TDEE'],0)."</td>
                                                <td>".$gen['jumlahPairingAwal']."</td>
                                                <td>".$gen['jumlahMemenuhiKriteria']."</td>
                                                <td>".round($kalori_hasil,2)."</td>
                                            </tr>
                                        ";
                                    }
                                }
                                else{
                                    echo "
                                        <tr>
                                            <td>".$co."</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                        </tr>
                                    ";
                                }
                                $co++;
                            }
                        }
                        
                        
                    }
                }
            }
        }


        foreach($umur as $usia){
            foreach($goals as $gl){
                foreach($aktivitas as $ak){
                    foreach($orientasiMakan as $om){
                        if($usia == 17){
                            foreach($beratCewekRemaja as $berat){
                                $gen = $this->generate_SET("lowKarbo",159,$berat,$ak,$gl,"F",$usia,$om);
                                if($gen['rekomen_final']  != null){
                                    foreach($gen['rekomen_final'] as $key => $kalori_hasil){
                                        echo "
                                            <tr>
                                                <td>".$co."</td>
                                                <td>".round($gen['tdee'],0)."</td>
                                                <td>".round($gen['batasBawah_TDEE'],0)."</td>
                                                <td>".round($gen['batasAtas_TDEE'],0)."</td>
                                                <td>".$gen['jumlahPairingAwal']."</td>
                                                <td>".$gen['jumlahMemenuhiKriteria']."</td>
                                                <td>".round($kalori_hasil,2)."</td>
                                            </tr>
                                        ";
                                    }
                                }
                                else{
                                    echo "
                                        <tr>
                                            <td>".$co."</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                        </tr>
                                    ";
                                }
                                $co++;
                            }
                        }
                        else{
                            foreach($beratCewekDewasa as $berat){
                                $gen = $this->generate_SET("lowKarbo",159,$berat,$ak,$gl,"M",$usia,$om);
                                if($gen['rekomen_final']  != null){
                                    foreach($gen['rekomen_final'] as $key => $kalori_hasil){
                                        echo "
                                            <tr>
                                                <td>".$co."</td>
                                                <td>".round($gen['tdee'],0)."</td>
                                                <td>".round($gen['batasBawah_TDEE'],0)."</td>
                                                <td>".round($gen['batasAtas_TDEE'],0)."</td>
                                                <td>".$gen['jumlahPairingAwal']."</td>
                                                <td>".$gen['jumlahMemenuhiKriteria']."</td>
                                                <td>".round($kalori_hasil,2)."</td>
                                            </tr>
                                        ";
                                    }
                                }
                                else{
                                    echo "
                                        <tr>
                                            <td>".$co."</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>0</td>
                                        </tr>
                                    ";
                                }
                                $co++;
                            }
                        }
                    }
                }
            }
        }
        echo"</tbody>
        </table>";
    }

    
    public function generate_SET($komposisi, $tb, $bb, $aktifitas, $goal, $gender,$usia, $orinetasiMakan){
        $nutrisi = $this -> getTDEE($tb, $bb, $aktifitas, $goal, $gender,$usia);

        $pref = $orinetasiMakan;

        $komposisiPilih = $komposisi;

        $AHP_Breakfast = $this->AHP_SORT_SET($nutrisi['tdeeBersih'],$pref,$komposisiPilih,"breakfast");
        $AHP_Lunch = $this->AHP_SORT_SET($nutrisi['tdeeBersih'],$pref,$komposisiPilih,"lunch");
        $AHP_Dinner = $this->AHP_SORT_SET($nutrisi['tdeeBersih'],$pref,$komposisiPilih,"dinner");

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

            return [
                'rekomen_final'=> $rekomen_final, 
                "rekomen_final_detail" => $rekomen_final_detail, 
                "jumlahPairingAwal" => $jumlahPairingAwal,
                'jumlahMemenuhiKriteria' => $jumlahMemenuhiKriteria,
                'tdee' => $nutrisi['tdeeBersih'],
                'batasAtas_TDEE'=> $batasAtas_TDEE,
                'batasBawah_TDEE' => $batasBawah_TDEE
            ];
        }
        else{
            return ['rekomen_final'=> null, "rekomen_final_detail" => null];
        }
        
        

        
    }
    public function AHP_SORT_SET($TDEE, $preferance, $tipe_limitProfile,$template_group){
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
                array_push($appropriate,array('score_a'=>$P_a, 'score_lp'=>$P_lp, 'idResep'=>$idResep, ''));
            }
            else{// inappropriate
                array_push($in_appropriate,$idResep);
            }
            
            array_push($listResepIdentity,$resepIdentity);
            array_push($alternatifResep,$resepKarakter);
        }

        foreach($appropriate as $theID){
            foreach($listResepIdentity as $resepComplete){
                if($theID['idResep'] == $resepComplete['idResep']){
                    array_push($menuRecomen, $resepComplete);
                } 
            }
        }

        
        return $menuRecomen;
        // return dd($menuRecomen);
    }


    //START UJI BIASA
    public function ujiAHP_1(){
        ini_set('max_execution_time', 300000000);
        $umur = [17,21,25,26,30,35,36,40,45];
        $goals = ["maintain","cutting","bulking"];
        // $pref = ["highKarbo","moderateKarbo","lowKarbo"];
        $orientasiMakan = ["normal","vegetarian","vegan"];
        $aktivitas = [1,2,3,4,5];
        $beratCowokRemaja = [49,50,70,71,80,81];
        $beratCowokDewasa = [52,53,70,71,84,85];

        $beratCewekRemaja = [43,44,64,65,76,77];
        $beratCewekDewasa = [46,47,63,64,75,76];

        // echo "<h1>Pria</h1>";
        echo '  <table>
                    <thead>
                        <tr align="center">
                            <th>No</th>
                            <th>Gender</th>
                            <th>Tinggi</th>
                            <th>Usia</th>
                            <th>Berat</th>
                            <th>Goals</th>
                            <th>Aktivitas</th>
                            <th>TDEE</th>
                            <th>Orientasi</th>
                            <th>Untuk</th>
                            <th>Karbo</th>
                            <th>Protein</th>
                            <th>Lemak</th>
                        </tr>
                    </thead>
                
                    <tbody>';
        $co = 1;
        foreach($umur as $usia){
            foreach($goals as $gl){
                foreach($aktivitas as $ak){
                    foreach($orientasiMakan as $om){
                        if($usia == 17){
                            foreach($beratCowokRemaja as $berat){
                                $gen = $this->generate("lowKarbo",168,$berat,$ak,$gl,"M",$usia,$om);
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Pria</td>
                                    <td>168</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(168,$berat,$ak,$gl,'M',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Breakfast</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['karbo']) ? 0 : round($gen['list_Breakfast'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['protein']) ? 0 : round($gen['list_Breakfast'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['lemak']) ? 0 : round($gen['list_Breakfast'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Pria</td>
                                    <td>168</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(168,$berat,$ak,$gl,'M',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Lunch</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['karbo']) ? 0 : round($gen['list_Lunch'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['protein']) ? 0 : round($gen['list_Lunch'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['lemak']) ? 0 : round($gen['list_Lunch'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Pria</td>
                                    <td>168</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(168,$berat,$ak,$gl,'M',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Dinner</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['karbo']) ? 0 : round($gen['list_Dinner'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['protein']) ? 0 : round($gen['list_Dinner'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['lemak']) ? 0 : round($gen['list_Dinner'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                            }
                        }
                        else{
                            foreach($beratCowokDewasa as $berat){
                                $gen = $this->generate("lowKarbo",168,$berat,$ak,$gl,"M",$usia,$om);
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Pria</td>
                                    <td>168</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(168,$berat,$ak,$gl,'M',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Breakfast</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['karbo']) ? 0 : round($gen['list_Breakfast'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['protein']) ? 0 : round($gen['list_Breakfast'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['lemak']) ? 0 : round($gen['list_Breakfast'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Pria</td>
                                    <td>168</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(168,$berat,$ak,$gl,'M',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Lunch</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['karbo']) ? 0 : round($gen['list_Lunch'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['protein']) ? 0 : round($gen['list_Lunch'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['lemak']) ? 0 : round($gen['list_Lunch'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Pria</td>
                                    <td>168</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(168,$berat,$ak,$gl,'M',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Dinner</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['karbo']) ? 0 : round($gen['list_Dinner'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['protein']) ? 0 : round($gen['list_Dinner'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['lemak']) ? 0 : round($gen['list_Dinner'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                            }
                        }
                        
                        
                    }
                }
            }
        }


        foreach($umur as $usia){
            foreach($goals as $gl){
                foreach($aktivitas as $ak){
                    foreach($orientasiMakan as $om){
                        if($usia == 17){
                            foreach($beratCewekRemaja as $berat){
                                $gen = $this->generate("lowKarbo",159,$berat,$ak,$gl,"F",$usia,$om);
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Wanita</td>
                                    <td>159</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(159,$berat,$ak,$gl,'F',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Breakfast</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['karbo']) ? 0 : round($gen['list_Breakfast'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['protein']) ? 0 : round($gen['list_Breakfast'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['lemak']) ? 0 : round($gen['list_Breakfast'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Wanita</td>
                                    <td>159</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(159,$berat,$ak,$gl,'F',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Lunch</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['karbo']) ? 0 : round($gen['list_Lunch'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['protein']) ? 0 : round($gen['list_Lunch'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['lemak']) ? 0 : round($gen['list_Lunch'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Wanita</td>
                                    <td>159</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(159,$berat,$ak,$gl,'F',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Dinner</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['karbo']) ? 0 : round($gen['list_Dinner'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['protein']) ? 0 : round($gen['list_Dinner'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['lemak']) ? 0 : round($gen['list_Dinner'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                            }
                        }
                        else{
                            foreach($beratCewekDewasa as $berat){
                                $gen = $this->generate("lowKarbo",159,$berat,$ak,$gl,"M",$usia,$om);
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Wanita</td>
                                    <td>159</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(159,$berat,$ak,$gl,'F',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Breakfast</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['karbo']) ? 0 : round($gen['list_Breakfast'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['protein']) ? 0 : round($gen['list_Breakfast'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Breakfast'][0]['macro']['lemak']) ? 0 : round($gen['list_Breakfast'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Wanita</td>
                                    <td>159</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(159,$berat,$ak,$gl,'F',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Lunch</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['karbo']) ? 0 : round($gen['list_Lunch'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['protein']) ? 0 : round($gen['list_Lunch'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Lunch'][0]['macro']['lemak']) ? 0 : round($gen['list_Lunch'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                                echo "
                                    <tr>
                                    <td>".$co."</td>
                                    <td>Wanita</td>
                                    <td>159</td>
                                    <td>".$usia."</td>
                                    <td>".$berat."</td>
                                    <td>".$gl."</td>
                                    <td>".$ak."</td>
                                    <td>".($this->getTDEE(159,$berat,$ak,$gl,'F',$usia))['tdeeBersih']."</td>
                                    <td>".$om."</td>
                                    <td>Dinner</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['karbo']) ? 0 : round($gen['list_Dinner'][0]['macro']['karbo'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['protein']) ? 0 : round($gen['list_Dinner'][0]['macro']['protein'],0))."</td>
                                    <td>".(empty($gen['list_Dinner'][0]['macro']['lemak']) ? 0 : round($gen['list_Dinner'][0]['macro']['lemak'],0))."</td>
                                    </tr>
                                ";
                                $co++;
                            }
                        }
                    }
                }
            }
        }
        echo"</tbody>
        </table>";
    }

    public function generate($komposisi, $tb, $bb, $aktifitas, $goal, $gender,$usia, $orinetasiMakan){
        $nutrisi = $this -> getTDEE($tb, $bb, $aktifitas, $goal, $gender,$usia);

        $pref = $orinetasiMakan;

        $komposisiPilih = $komposisi;

        $AHP_Breakfast = $this->AHP_SORT($nutrisi['tdeeBersih'],$pref,$komposisiPilih,"breakfast");
        $AHP_Lunch = $this->AHP_SORT($nutrisi['tdeeBersih'],$pref,$komposisiPilih,"lunch");
        $AHP_Dinner = $this->AHP_SORT($nutrisi['tdeeBersih'],$pref,$komposisiPilih,"dinner");

        $list_Breakfast = $AHP_Breakfast;
        $list_Lunch = $AHP_Lunch;
        $list_Dinner = $AHP_Dinner;
        

        return 
            [   'list_Breakfast'=> $list_Breakfast,
                'list_Lunch'=> $list_Lunch,
                'list_Dinner'=> $list_Dinner,
            ];
    }
    
    public function AHP_SORT($TDEE, $preferance, $tipe_limitProfile,$template_group){
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
        if(count($appropriate)>0){
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
            $rand_keys = (array)array_rand($menuRecomen, $jumlahRand);
            $sampleMenu = [];
            foreach($rand_keys as $index){
                array_push($sampleMenu, $menuRecomen[$index]);
            }
            $menuRecomen = $sampleMenu;
        }
        else{
            $menuRecomen = null;
        }
        

        
        return $menuRecomen;
        // return dd($menuRecomen);
    }
    //END UJI BIASA



    public function getTDEE($tb, $bb, $aktifitas, $goal, $gender,$usia){
        $tinggiBadan = $tb;
        $beratBadan = $bb;
        $tingkat_aktivitas = $aktifitas;
        $goal = $goal;
        $gender = $gender;
        $usia = $usia;

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

        return array('bmr'=>$bmr, 'tdee'=>$tdee, 'tdeeBersih'=>$tdeeBersih, 'usia'=>$usia);
        
    }
}


