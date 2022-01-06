@include('dashboard.sidenav')


<script>
    $("#generatePage").addClass("active");
</script>


<style>
    .titleMealPlan{
        font-size: 2.5rem;
        font-weight: 600;
    }
    #calenderWrapping{
        border-radius: .25em 0 0 .25em;
        border-right: unset;
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    .contentBody{
        margin: 30px 0;
    }
    .datepicker{
        cursor: pointer;
    }
    .dateContainer{
        margin-bottom: 20px;
    }
    #btnGenerate{
        margin-top: 20px;
        width: 100%;
    }
    .card-sub-title-value{
        font-size: 3rem;
        text-align: center;
    }
    .cardKomposisi{
        width: 18rem;
        cursor: pointer;
        border-radius: 30px;
        border: 2px solid #ececec !important;
        transition: .3s;
    }
    .cardKomposisi[komposisi="moderateKarbo"]{
        margin: 0 1rem;
    }
    .cardKomposisi:hover{
        box-shadow: 0px 0px 10px 3px rgba(0,0,0,.1) !important;
        border: 2px solid #17a2b8 !important;
    }
    .cardKomposisi.choose{
        background-color: #17a2b8;
        color: white;
        transition: .3s;
    }
    .cardKomposisi.choose hr{
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
        border-color: white;
        box-shadow: 0px 0px 10px 3px rgba(0,0,0,.1) !important;
    }
    .cardMenu{
        width: 100%;
        margin-bottom: 1em;
    }
    .gridContainerMenu{
        display: grid;
        grid-template-columns: auto auto auto auto auto;
        grid-column-gap: 1em;
    }
    .btnPilih{
        width: 100%;
        border-radius: 0;
    }
    .bntPilihContainer{
        padding: 0;
    }
    .accordion-item{
        border-bottom: 1px solid #17a2b8 !important;
        border-radius: 0 !important;
    }
    .titleGroup{
        margin-top: 100px;
    }
    .card-title , .card-text{
        font-size: 16px;
    }
    #btnSubmit{
        width: 100%;
        margin-top: 50px;
    }
    .accordContainer{
        padding: 0;
    }
    .cover_resep{
        width: 30%;
        float: left;
        border-radius: 50%;
    }
    .card-body, .card-header{
        padding: .7em;
    }
    .card-body{
        width: 100%;
        height: 100%;
        display: inline-block;
    }
    .card-body h6{
        width: 100%;
        float: left;
    }
    .card-header{
        font-size: 13px;
    }
    .menu_rightSide{
        width: 70%;
        float: right;
        padding-left: .5em;
    }
    .menu_rightSide table{
        font-size: 12px;
    }
    .card-title{
        font-size: 12px;
    }
    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
        .accordContainer {
            padding: 0;
        }
        .cardMenu {
            width: 55vw;
            margin-bottom: 1em;
            display: inline-block;
            white-space: normal;
        }
        .gridContainerMenu {
            grid-template-columns: unset;
            grid-column-gap: unset;
            width: 100%;
            overflow-x: scroll;
            white-space: nowrap;
            display: inline-block;
        }
        .accordion-body{
            padding-bottom: 5em;
        }
    }
</style>

<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Generate Meal Planner</span>
                </div>
            </div>
	
            <div class="row contentBody">
                <div class="col-sm-12" style="padding: 0;">
                    <form method="POST" action="/generate-summary">
                        @csrf 
                        <?php $co = 1;?>
                        <div class="accordContainer">
                            <div class="alert alert-info" role="alert">
                               Pilih 1 untuk setiap tanggal !</b>
                            </div>
                        @foreach ($rekomendasi_akhir as $date => $value)
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-heading{{$co}}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$co}}" aria-expanded="false" aria-controls="flush-collapse{{$co}}">
                                        <?php echo hari(str_replace("_","-",$date));?>
                                    </button>
                                    </h2>
                                    <div id="flush-collapse{{$co}}" class="accordion-collapse collapse" aria-labelledby="flush-heading{{$co}}" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <input type="text" class="pilihBreakfast" name="pilihBreakfast[{{$date}}]" tanggal="{{$date}}" hidden required>
                                        <input type="text" class="pilihLunch" name="pilihLunch[{{$date}}]" tanggal="{{$date}}" hidden required>
                                        <input type="text" class="pilihDinner" name="pilihDinner[{{$date}}]" tanggal="{{$date}}" hidden required>
                                        <div class="gridContainerMenu">
                                            @foreach ($value as $index => $dataResep)
                                                <div class="card cardMenu">
                                                    <div class="card-header">
                                                        <?php
                                                            $b_karbo = $dataResep['breakfast']['karbo'];
                                                            $b_protein = $dataResep['breakfast']['protein'];
                                                            $b_lemak = $dataResep['breakfast']['fat'];
                                                            $b_kalori = $dataResep['breakfast']['kalori'];

                                                            $l_karbo = $dataResep['lunch']['karbo'];
                                                            $l_protein = $dataResep['lunch']['protein'];
                                                            $l_lemak = $dataResep['lunch']['fat'];
                                                            $l_kalori = $dataResep['lunch']['kalori'];

                                                            $d_karbo = $dataResep['dinner']['karbo'];
                                                            $d_protein = $dataResep['dinner']['protein'];
                                                            $d_lemak = $dataResep['dinner']['fat'];
                                                            $d_kalori = $dataResep['dinner']['kalori'];

                                                            $t_karbo = $b_karbo + $l_karbo + $d_karbo;
                                                            $t_protein = $b_protein + $l_protein + $d_protein;
                                                            $t_lemak = $b_lemak + $l_lemak + $d_lemak;
                                                            $t_kalori = $b_kalori + $l_kalori + $d_kalori;
                                                        ?>
                                                        <table style="width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>Kalori</td>
                                                                    <td align="right">{{round($t_kalori,0)}} kcal</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Karbo</td>
                                                                    <td align="right">{{round($t_karbo,0)}} gram</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Protein</td>
                                                                    <td align="right">{{round($t_protein,0)}} gram</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lemak</td>
                                                                    <td align="right">{{round($t_lemak,0)}} gram</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="card-body">
                                                        <h6>Breakfast</h6>
                                                        <div class="menu-container">
                                                            <img src="{{$dataResep['breakfast']['data_resep']['cover']}}" class="cover_resep" alt="{{$dataResep['breakfast']['data_resep']['judul_resep']}}">
                                                            
                                                            <div class="menu_rightSide" >
                                                                <h5 class="card-title">{{ucwords(strtolower($dataResep['breakfast']['data_resep']['judul_resep']))}}</h5>
                                                                <table style="width: 100%;">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Kalori</td>
                                                                            <td align="right">{{round($dataResep['breakfast']['kalori'],0)}} kcal</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Karbo</td>
                                                                            <td align="right">{{round($dataResep['breakfast']['karbo'],0)}} gram</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Protein</td>
                                                                            <td align="right">{{round($dataResep['breakfast']['protein'],0)}} gram</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Lemak</td>
                                                                            <td align="right">{{round($dataResep['breakfast']['fat'],0)}} gram</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <h6>Lunch</h6>
                                                        <div class="menu-container">
                                                            <img src="{{$dataResep['lunch']['data_resep']['cover']}}" class="cover_resep" alt="{{$dataResep['lunch']['data_resep']['judul_resep']}}">
                                                            
                                                            <div class="menu_rightSide" >
                                                                <h5 class="card-title">{{ucwords(strtolower($dataResep['lunch']['data_resep']['judul_resep']))}}</h5>
                                                                <table style="width: 100%;">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Kalori</td>
                                                                            <td align="right">{{round($dataResep['lunch']['kalori'],0)}} kcal</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Karbo</td>
                                                                            <td align="right">{{round($dataResep['lunch']['karbo'],0)}} gram</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Protein</td>
                                                                            <td align="right">{{round($dataResep['lunch']['protein'],0)}} gram</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Lemak</td>
                                                                            <td align="right">{{round($dataResep['lunch']['fat'],0)}} gram</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <hr>

                                                        <h6>Dinner</h6>
                                                        <div class="menu-container">
                                                            <img src="{{$dataResep['dinner']['data_resep']['cover']}}" class="cover_resep" alt="{{$dataResep['dinner']['data_resep']['judul_resep']}}">
                                                            
                                                            <div class="menu_rightSide" >
                                                                <h5 class="card-title">{{ucwords(strtolower($dataResep['dinner']['data_resep']['judul_resep']))}}</h5>
                                                                <table style="width: 100%;">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Kalori</td>
                                                                            <td align="right">{{round($dataResep['dinner']['kalori'],0)}} kcal</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Karbo</td>
                                                                            <td align="right">{{round($dataResep['dinner']['karbo'],0)}} gram</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Protein</td>
                                                                            <td align="right">{{round($dataResep['dinner']['protein'],0)}} gram</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Lemak</td>
                                                                            <td align="right">{{round($dataResep['dinner']['fat'],0)}} gram</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer text-muted bntPilihContainer">
                                                        <a hidden class="btn btn-primary btnPilih" index="{{$index}}" type-btn="breakfast" idResep="{{$dataResep['breakfast']['data_resep']['idResep']}}"  tanggal="{{$date}}">Pilih</a>
                                                        <a hidden class="btn btn-primary btnPilih" index="{{$index}}" type-btn="lunch" idResep="{{$dataResep['lunch']['data_resep']['idResep']}}"  tanggal="{{$date}}">Pilih</a>
                                                        <a class="btn btn-primary btnPilih" index="{{$index}}" type-btn="dinner" idResep="{{$dataResep['dinner']['data_resep']['idResep']}}"  tanggal="{{$date}}">Pilih</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $co++;?>
                        @endforeach
                        </div>
                        <button class="btn btn-info" id="btnSubmit" type="submit">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
            

            
        </div>
    </div>
</main>

<script>
    $('.btnPilih[type-btn="dinner"]').click(function(){
        var tanggal = $(this).attr("tanggal");
        var index = $(this).attr("index");
        //dinner
        var idResepBreakfast = $('.btnPilih[index="'+index+'"][type-btn="breakfast"][tanggal="'+tanggal+'"]').attr("idResep");
        var idResepLunch = $('.btnPilih[index="'+index+'"][type-btn="lunch"][tanggal="'+tanggal+'"]').attr("idResep");
        var idResepDinner = $('.btnPilih[index="'+index+'"][type-btn="dinner"][tanggal="'+tanggal+'"]').attr("idResep");
        
        $('.pilihBreakfast[tanggal="'+tanggal+'"]').val(idResepBreakfast);
        $('.pilihLunch[tanggal="'+tanggal+'"]').val(idResepLunch);
        $('.pilihDinner[tanggal="'+tanggal+'"]').val(idResepDinner);

        $('.btnPilih[type-btn="dinner"][tanggal="'+tanggal+'"]').addClass("btn-primary");
        $('.btnPilih[type-btn="dinner"][tanggal="'+tanggal+'"]').removeClass("btn-success");
        $(this).removeClass("btn-primary");
        $(this).addClass("btn-success");

        console.log('dinner');
        console.log(idResepBreakfast);
        console.log(idResepLunch);
        console.log(idResepDinner);
        console.log(tanggal);
    });
</script>



@include('./footer')


<?php
function hari($param_tanggal){
    $time = strtotime($param_tanggal);
    $day = date('D',$time);
 
	switch($day){
		case 'Sun':
			$hari_ini = "Minggu";
		break;
 
		case 'Mon':			
			$hari_ini = "Senin";
		break;
 
		case 'Tue':
			$hari_ini = "Selasa";
		break;
 
		case 'Wed':
			$hari_ini = "Rabu";
		break;
 
		case 'Thu':
			$hari_ini = "Kamis";
		break;
 
		case 'Fri':
			$hari_ini = "Jumat";
		break;
 
		case 'Sat':
			$hari_ini = "Sabtu";
		break;
		
		default:
			$hari_ini = "";		
		break;
	}
 
	return "<div><b>".$hari_ini."</b>,<br>".date('d M Y',$time)."</div>";
 
}

?>