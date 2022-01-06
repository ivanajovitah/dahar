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
    .coverImage{
        width: 100%;
    }
    .cardMenu{
        width: 100%;
        border: unset;
    }
    .card-body{
        padding: 0;
        padding-top: 20px;
    }
    .gridContainerMenu{
        display: inline-block;
        width: 100%;
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
    .itemGroup{
        width: 25%;
        margin: 0 auto;
    }
    .groupContainer{
        display: inline-flex;
        width: 100%;
        padding: 1em 2em;
    }
    hr{
        margin: 5em 0;
        opacity: 0.1;
    }
    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
        .contentContainer {
            padding: 0;
        }
        .cardMenu {
            width: 100%;
            margin-bottom: 1em;
            display: inline-block;
            white-space: normal;
        }
        .groupContainer {
            display: inline-block;
            width: 100%;
            padding: 1em;
        }
        .itemGroup {
            width: 100%;
            margin: 0;
        }
        .card-img-top{
            width: 30%;
            float: left;
        }
        .card-body{
            width: 70%;
            padding: 0;
            padding-left: 1em;
            float: right;
        }
        hr {
            margin: 1em 0;
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
                    <form method="POST" action="/generate-feedback">
                        @csrf 
                        <?php $co = 1;$length = count($listPlan);?>
                        <div class="contentContainer">
                        @foreach ($listPlan as $date => $value)
                            <h3><?php echo hari(str_replace("_","-",$date));?></h3>
                                <div class="groupContainer">
                                    <div class="itemGroup">
                                        <h5>Breakfast</h5>
                                        <input type="text" name="listResep[{{$date}}][breakfast]" value="{{$value['breakfast']['idResep']}}" hidden required>
                                        <div class="gridContainerMenu">
                                            <div class="card cardMenu">
                                                <img src="{{$value['breakfast']['cover']}}" class="card-img-top" alt="{{$value['breakfast']['judul_resep']}}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ucwords(strtolower($value['breakfast']['judul_resep']))}}</h5>
                                                    <p class="card-text" style="margin-bottom: 0">Serving: {{round($value['breakfast']['totalWeight'],0)}} gram</p>
                                                    <p class="card-text">{{round($value['breakfast']['calories'],0)}} kcal</p>
                                                    <p class="card-text">
                                                        <table style="width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>Karbo</td>
                                                                    <td align="right">
                                                                        {{round($value['breakfast']['macro']['karbo'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Protein</td>
                                                                    <td align="right">
                                                                        {{round($value['breakfast']['macro']['protein'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lemak</td>
                                                                    <td align="right">
                                                                        {{round($value['breakfast']['macro']['lemak'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="itemGroup">
                                        <h5>Lunch</h5>
                                        <input type="text" name="listResep[{{$date}}][lunch]" value="{{$value['lunch']['idResep']}}" hidden required>
                                        <div class="gridContainerMenu">
                                            <div class="card cardMenu">
                                                <img src="{{$value['lunch']['cover']}}" class="card-img-top" alt="{{$value['lunch']['judul_resep']}}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ucwords(strtolower($value['lunch']['judul_resep']))}}</h5>
                                                    <p class="card-text" style="margin-bottom: 0">Serving: {{round($value['lunch']['totalWeight'],0)}} gram</p>
                                                    <p class="card-text">{{round($value['lunch']['calories'],0)}} kcal</p>
                                                    <p class="card-text">
                                                        <table style="width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>Karbo</td>
                                                                    <td align="right">
                                                                        {{round($value['lunch']['macro']['karbo'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Protein</td>
                                                                    <td align="right">
                                                                        {{round($value['lunch']['macro']['protein'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lemak</td>
                                                                    <td align="right">
                                                                        {{round($value['lunch']['macro']['lemak'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="itemGroup">
                                        <h5>Dinner</h5>
                                        <input type="text" name="listResep[{{$date}}][dinner]" value="{{$value['dinner']['idResep']}}" hidden required>
                                        <div class="gridContainerMenu">
                                            <div class="card cardMenu">
                                                <img src="{{$value['dinner']['cover']}}" class="card-img-top" alt="{{$value['dinner']['judul_resep']}}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ucwords(strtolower($value['dinner']['judul_resep']))}}</h5>
                                                    <p class="card-text" style="margin-bottom: 0">Serving: {{round($value['dinner']['totalWeight'],0)}} gram</p>
                                                    <p class="card-text">{{round($value['dinner']['calories'],0)}} kcal</p>
                                                    <p class="card-text">
                                                        <table style="width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>Karbo</td>
                                                                    <td align="right">
                                                                        {{round($value['dinner']['macro']['karbo'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Protein</td>
                                                                    <td align="right">
                                                                        {{round($value['dinner']['macro']['protein'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lemak</td>
                                                                    <td align="right">
                                                                        {{round($value['dinner']['macro']['lemak'],0)}} gram
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <?php 
                                if($co != $length){
                                        echo '<hr>';
                                }
                                $co++;
                            ?>
                
                        @endforeach
                        </div>
                        <button class="btn btn-info" id="btnSubmit" type="submit">
                            Save
                        </button>
                    </form>
                </div>
            </div>
            

            
        </div>
    </div>
</main>


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
 
	return "<div><b>".$hari_ini."</b>, ".date('d M Y',$time)."</div>";
 
}

?>