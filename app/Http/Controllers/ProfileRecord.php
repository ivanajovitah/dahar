<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DateTime;

class ProfileRecord extends Controller
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
        $orientasiMakan = $userProfileSelect->orientasi_makanan;
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
    public function updateBasic(Request $request){
        $userId = Auth::id();
        DB::table('users')
              ->where('id', $userId)
              ->update([
                  'jenis_kelamin' => $request['gender'], 
                  'tanggal_lahir'=> $request['tglLahir']
                ]);
        return redirect('/track');
    }
    public function updateProfile(Request $request){
        $userId = Auth::id();

        if(!isset($request->healtLabel)){
            $healtLabel=null;
        }
        else{
            $healtLabel = implode(",",$request->healtLabel);
        }   

        $insertData = array(
            "idUser" => $userId,
            "tinggi_badan" => $request['tinggiBadan'],
            "berat_badan" => $request['beratBadan'],
            "orientasi_makanan" => $request['preferance'],
            "tingkat_aktivitas" => $request['tingkatAktifitas'],
            "label" => $healtLabel,
            "goal" => $request['goal'],
            "kota" => $request['kota'],
            "provinsi" => $request['provinsi'],
            "created_at" => date("Y-m-d"),
        );
        DB::table('user_profile')->insert($insertData);
        return redirect('/track');
    }
    public function showProfile(){

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

        $gender = $selectUsers->jenis_kelamin;
        $tglLahir = $selectUsers->tanggal_lahir;

        // Cari Provinsi Start
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "key: 9bf09d7b37cb456f6ee156e70a22d4a6"
            ),
        ));

        $responseProv = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // Cari Provinsi End
        
        $meta = "";
        if($userProfileSelect == null && $gender == null && $tglLahir == null){
            $meta =  "kosong__all"; 
            $provinceTeks = "";
            $tglLahir = "";
            $gender = "";
            $today = "";
            $nutrisiUser = "";
        }
        elseif( $gender == null || $tglLahir == null){
            $meta =  "kosong__users";
            $provinceTeks = "";
            $tglLahir = "";
            $gender = "";
            $today = "";
            $nutrisiUser = "";
        }
        elseif($userProfileSelect == null){
            $meta =  "kosong__userProfile";
            $provinceTeks = "";
            $tglLahir = "";
            $gender = "";
            $today = "";
            $nutrisiUser = "";
        }
        else{
            $meta = "ada";

            $hasilProvinsi = json_decode($responseProv)->rajaongkir->results;

            ///Cari Nama Provinsi Start
            $provinceTeks = "";
            foreach($hasilProvinsi as $provLoop){
                if($provLoop->province_id == $userProfileSelect->provinsi){
                    $provinceTeks = ($provLoop->province);
                }
            }
            ///Cari Nama Provinsi END
            

            ///Cari Kota Start
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.rajaongkir.com/starter/city?province=".$userProfileSelect->provinsi,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "key: 9bf09d7b37cb456f6ee156e70a22d4a6"
                ),
            ));

            $responseKota = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $hasilKota = json_decode($responseKota)->rajaongkir->results;

            foreach($hasilKota as $kotaLoop){
                if($kotaLoop->city_id == $userProfileSelect->kota){
                    $provinceTeks = ($kotaLoop->city_name).", ".$provinceTeks;
                }
            }
            ///Cari Kota End

            $nutrisiUser = $this -> getTDEE();
            
            
            $today = date("Y-m-d");
        }


        $healtLabel = array(
                    "Sugar-Conscious" => "berhati-hati dengan gula",
                    "Keto-Friendly" => "cocok untuk yang Diet Keto",
                    "Vegan" => "tidak konsumsi daging hewani & produk dari hewan",
                    "Vegetarian" => "masih konsumsi produk dari hewan, tetapi tidak konsumsi daging hewani",
                    "Mediterranean" => "sumber utama dari tumbuhan, kandungan daging protein sedikit",
                    "Dairy-Free" => "tidak mengkonsumsi susu",
                    "Gluten-Free" => "tidak konsumsi makanan mengandung protein gluten seperti gandum dan tepung",
                    "Egg-Free" => "tidak mengandung telur",
                    "Tree-Nut-Free" => "aman untuk penderita allergi kacang-kacangan",
                    "Fish-Free" => "tidak mengandung ikan",
                    "Pork-Free" => "tidak mengandung babi",
                    "Red-Meat-Free" => "tidak mengandung hewani daging merah",
                    "Crustacean-Free" => "tidak mengandung sejenis udang, kepiting, lobster",
                    "Lupine-Free" => "aman untuk penderita allergi kacang-kacangan, terkusus kacang meditarian",
                    "Mollusk-Free" => "tidak mengandung sejening kerang, cumi, ubur-ubur",
                    "Alcohol-Free" => "tidak mengandung alkohol",
                    "Paleo" => "tidak mengkonsumsi produk olahan, kacang, susu, gula, garam",
                    "DASH" => "cocok untuk penderita hipertensi");
        ksort($healtLabel);

        return view('dashboard.userPofile',array(
                    'meta' => $meta,
                    'err' => $err, 
                    'response' => $responseProv, 
                    'userProfileSelect'=>$userProfileSelect, 
                    'domisili' => $provinceTeks,
                    'tglLahir'=>$tglLahir,
                    'gender' => $gender,
                    'today' => $today,
                    'nutrisi' => $nutrisiUser,
                    'healtLabel' => $healtLabel
                ));
    }

    public function cariKota(Request $request){
        $idProve = $request->id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/city?province=".$idProve,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 9bf09d7b37cb456f6ee156e70a22d4a6"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $arrayCheck = array();

        if ($err) {
        } else {
            $kotaRespon = json_decode($response, true);
            $kota = $kotaRespon["rajaongkir"]["results"];

            echo '<option value="pilih">--- pilih kota ---</option>';

            foreach ($kota as $key => $value) {
                if (in_array($value["city_name"], $arrayCheck)){
                }else{
                    array_push($arrayCheck, $value["city_name"]);
                    echo '<option value="'.$value["city_id"].'">'.$value["city_name"].'</option>';
                }
                
            }
        }
    }
}
