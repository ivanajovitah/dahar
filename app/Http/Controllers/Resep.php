<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Resep extends Controller
{
    public function saveMenu(Request $request){
        if($request->ajax()){
            $userId = Auth::id();

            DB::table('save_menu')->insert([
                'id_user' => $userId,
                'id_menu' => $request['id'],
                'created_at' => date("Y-m-d")
            ]);

            return "sukses";
        }
    }

    public function unsaveMenu(Request $request){
        if($request->ajax()){
            $userId = Auth::id();

            DB::table('save_menu')
                ->where('id_menu','=',$request['id'])
                ->where('id_user','=',$userId)
                ->delete();

            return "sukses";
        }
    }

    public function likeMenu(Request $request){
        if($request->ajax()){
            $userId = Auth::id();

            DB::table('like_menu')->insert([
                'id_user' => $userId,
                'id_menu' => $request['id'],
                'created_at' => date("Y-m-d")
            ]);

            return "sukses";
        }
    }

    public function unlikeMenu(Request $request){
        if($request->ajax()){
            $userId = Auth::id();

            DB::table('like_menu')
                ->where('id_menu','=',$request['id'])
                ->where('id_user','=',$userId)
                ->delete();

            return "sukses";
        }
    }

    public function helth_label(){
        $list_komposisi = DB::table('list_resep')->select('healthLabels' )->get();
        
        foreach($list_komposisi as $list){
            foreach(json_decode($list->healthLabels) as $key => $value){
                echo $value."<br>";
            }
        }
    }

    public function save_buatResep(Request $request){
        $userId = Auth::id();
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            "nama"    => [
                    'required',
                    'array', // input must be an array
                    'min:1'  // there must be three members in the array
            ],
            "cuisineType"    => [
                'required',
                'array',
                'min:1'  // there must be three members in the array
            ],
            "healtLabel"    => [
                'required',
                'array',
                'min:1'  // there must be three members in the array
            ],
        ]);

        $judulResep= $request->judul;
        $jumlah_sajian = $request->jumlah_sajian;
        $bahan_qty = $request->bahan_qty;
        $namaResep = $request->nama;
        $energi = $request->energi;
        $energi = $request->energi;
        $protein = $request->protein;
        $karbo = $request->karbo;
        $lemak = $request->lemak;
        $tipe = $request->tipe;
        $langkah = $request->langkah;
        $h_label = $request->healtLabel;
        $c_type = $request->cuisineType;

        $bahanTeks = '[';
        $bahanLineTeks = '[';
        $healtLabelTeks = '[';
        $c_typeTeks = '[';

        $totalProtein = 0;
        $totalKarbo = 0;
        $totalLemak = 0;
        $totalKalori = 0;

        $con = 1;
        foreach($namaResep as $key => $value){
            if($con == count($namaResep)){
                $bahanTeks = $bahanTeks.'{"text": "'.$bahan_qty[$key].' gram '.$value.'","quantity": '.$bahan_qty[$key].', "measure": "gram", "food": "'.$value.'", "weight": '.$bahan_qty[$key].', "foodCategory": "'.$tipe[$key].'", "foodId": "", "image": ""}';
                  $bahanLineTeks = $bahanLineTeks.'"'.$bahan_qty[$key].' gram '.$value.'"';
            }
            else{
                $bahanTeks = $bahanTeks.'{"text": "'.$bahan_qty[$key].' gram '.$value.'","quantity": '.$bahan_qty[$key].', "measure": "gram", "food": "'.$value.'", "weight": '.$bahan_qty[$key].', "foodCategory": "'.$tipe[$key].'", "foodId": "", "image": ""},';

                  $bahanLineTeks = $bahanLineTeks.'"'.$bahan_qty[$key].' gram '.$value.'",';
            }
            $totalProtein = $totalProtein + (($protein[$key]/100)*$bahan_qty[$key]);
            $totalKarbo = $totalProtein + (($karbo[$key]/100)*$bahan_qty[$key]);
            $totalLemak = $totalProtein + (($lemak[$key]/100)*$bahan_qty[$key]);
            $totalKalori = $totalProtein + (($totalProtein * 4) + ($totalKarbo * 4) + ($totalLemak * 9));
            $con++;
        }

        $con = 1;
        foreach($h_label as $label){
            if($con == count($h_label)){
                $healtLabelTeks = $healtLabelTeks.'"'.$label.'"';
            }
            else{
                $healtLabelTeks = $healtLabelTeks.'"'.$label.'",';
            }
            $con++;
        }

        $con = 1;
        foreach($c_type as $label){
            if($con == count($c_type)){
                $c_typeTeks = $c_typeTeks.'"'.$label.'"';
            }
            else{
                $c_typeTeks = $c_typeTeks.'"'.$label.'",';
            }
            $con++;
        }

        $bahanTeks = $bahanTeks.']';
        $bahanLineTeks = $bahanLineTeks.']';
        $healtLabelTeks = $healtLabelTeks.']';
        $c_typeTeks = $c_typeTeks.']';

        $digestTeks = '[{"label": "Fat","total": '.$totalLemak.'},{"label": "Carbs","total": '.$totalKarbo.'},{"label": "Protein","total": '.$totalProtein.'}]';
        
        $imageName = time().'.'.$request->image->extension();  

        $request->image->move(public_path('cover_resep'), $imageName);

        DB::table('list_resep')->insert([
            'judul_resep' => $judulResep,
            'url' => "",
            'cover' => '/cover_resep/'.$imageName,
            'yield' => $jumlah_sajian,
            'bahan' => $bahanTeks,
            'bahanLines' => $bahanLineTeks,
            'langkah' => $langkah,
            'calories' => $totalKalori,
            'digest' =>  $digestTeks,
            'likes' =>  0,
            'totalWeight' => 0,
            'score' => 0,
            'totalTime' => 0,
            'mealType' => 0,
            'cuisineType' => $c_typeTeks,
            'healthLabels' => $healtLabelTeks,
            'totalNutrients' => "",
            'totalDaily' => "",
            'idAuthor' => $userId,
            'created_at' => date("Y-m-d")
        ]);

        return redirect('/koleksi');
        
    }

    public function direectKeBuatResep(){
        return redirect('/buat-resep');
    }

    public function resep_show(){
        $selectResep = DB::table('list_resep')->inRandomOrder()->get();

        return view('dashboard.cari_resep',['selectResep'=> $selectResep]);
    }

    public function swow_buatResep(){

        $list_komposisi = DB::table('table_komposisi')->select('id','energi','karbo','protein','lemak','makanan', 'nama_komposisi')->get();
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
        return view('dashboard.buat_resep',[
            'list_komposisi'=> $list_komposisi,
            'healtLabel' => $healtLabel
        ]);
    }

    public function cari_resep($id){
        $userId = Auth::id();

        $selectResep = DB::table('list_resep')
                        ->leftJoin('users', 'list_resep.idAuthor', '=', 'users.id')
                        ->select('list_resep.*', 'users.name')
                        ->where('list_resep.id','=',$id)
                        ->orderBy('list_resep.id', 'desc')
                        ->first();

        $banyakSave = DB::table('save_menu')
                        ->where('id_menu','=',$selectResep->id)
                        ->orderBy('id', 'desc')
                        ->count();

        $banyakLike = DB::table('like_menu')
                        ->where('id_menu','=',$selectResep->id)
                        ->orderBy('id', 'desc')
                        ->count();

        $idLike = DB::table('like_menu')
                        ->where('id_menu','=',$selectResep->id)
                        ->where('id_user','=',$userId)
                        ->orderBy('id', 'desc')
                        ->first();

        $idSave = DB::table('save_menu')
                        ->where('id_menu','=',$selectResep->id)
                        ->where('id_user','=',$userId)
                        ->orderBy('id', 'desc')
                        ->first();

        return view('dashboard.view_resep',[
                    'selectResep'=> $selectResep,
                    'banyakLike'=> $banyakLike,
                    'banyakSave'=> $banyakSave,
                    'idLike' => $idLike,
                    'idSave' => $idSave
                ]);
    }


    public function koleksi(){
        $userId = Auth::id();

        $selectResep = DB::table('list_resep')
                        ->leftJoin('users', 'list_resep.idAuthor', '=', 'users.id')
                        ->where('users.id','=',$userId)
                        ->select('list_resep.id')
                        ->get();

        $listLike = DB::table('like_menu')
                        ->where('id_user','=',$userId)
                        ->get();

        $listSave = DB::table('save_menu')
                        ->where('id_user','=',$userId)
                        ->get();
        
        $finalResep = [];
        $checkArray = [];

        foreach($selectResep as $resep => $value){
            $dataCategory = array("list_resep");
            $checkArray[$value->id] = $dataCategory;
        }

        foreach($listLike as $indexLike => $valueLike){
            if(array_key_exists($valueLike->id_menu, $checkArray)){
                array_push($checkArray[$valueLike->id_menu],"like");
            }
            else{
                $dataCategory = array("like");
                $checkArray[$valueLike->id_menu] = $dataCategory;
            }
        }

        foreach($listSave as $indexLike => $valueSave){
            if(array_key_exists($valueSave->id_menu, $checkArray)){
                array_push($checkArray[$valueSave->id_menu],"save");
            }
            else{
                $dataCategory = array("save");
                $checkArray[$valueSave->id_menu] = $dataCategory;
            }
        }
    
        foreach($checkArray as $idResep => $values){
            $dataResep = DB::table('list_resep')
                            ->where("id","=",$idResep)
                            ->first();
            $datas = array(
                "dataResep" => $dataResep,
                "kategori" => $checkArray[$idResep],
            );

            array_push($finalResep, $datas);
        }
        // dd($finalResep);

        return view('dashboard.koleksi',[
                    'listResep'=> $finalResep,
                ]);
    }
}
