@include('dashboard.sidenav')

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    $("#plannerPage").addClass("active");
</script>

<style>
    .titleMealPlan{
        font-size: 2.5rem;
        font-weight: 600;
    }
    .sectionMarign{
        margin: 30px 0;
    }
    #periodeDropDown{
        width: 100%;
        text-align: left;
    }
    #periodeDropDown::after{
        display: none;
    }
    #containerMovePeriode{
        margin-left: 2rem;
    }
    #btnGenerate{
        float: right;
    }
    .cardWeekly{
        border-radius: 20px;
        padding: 20px;
        box-shadow: 1px 1px 2px 3px rgba(0,0,0,.07) !important;
        transition: .7s;
    }
    .cardWeekly:not([class*="noHover"]):hover{
        box-shadow: 1px 1px 4px 2px rgba(0,0,0,.37) !important;
        transition: .7s;
    }
    .cardWeekly .containerTanggal{
        border-bottom: 2px solid rgba(0,0,0,.07);
        margin-bottom: 20px;
    }
    .leftCardContainer{
        display: inline-block;
    }
    .grafSection, .grafDetailSection{
        display: inline-block;
        width: 100%;
    }
    .grafSection img{
        width: 100%;
    }
    .grafDetailSection .textCOntainer{
        width: 100%;
        font-weight: 400;
        font-size: 20px;
    }
    .grafDetailSection .textCOntainer .giziText{
        margin: 5px 0;
        display: inline-block;
        width: 100%;
    }
    .grafDetailSection .textCOntainer .giziText .label{
        float:left;
    }
    .grafDetailSection .textCOntainer .giziText .measure{
        float:right;
    }
    .grafDetailSection .textCOntainer .giziText.proteinText{
        color: #976FE8;
    }
    .containerMenu{
        height: 100%;
        cursor: pointer;
    }
    .containerMenu h5{
        font-weight: 600;
        text-align: center;
    }
    .containerMenu h6,.containerMenu p{
        font-weight: 300;
        text-align: center;
        color: black;
    }
    .containerMenu.containerLunch{
        border-left: 2px solid rgba(0,0,0,.07);
        border-right: 2px solid rgba(0,0,0,.07);
    }
    .containerMenu .menuCover{
        width: 100%;
        border-radius: 50%;
    }
    .textContainer{
        margin-top: 10px;
    }
    .linkResep{
        color: black;
    }
    .linkResep:hover{
        color: #96d7c6;
    }
    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
        #containerMovePeriode{
            margin: 1rem 0 2rem 0;
        }
        #btnGenerate{
            float: left;
        }
        .leftCardContainer {
            display: block;
            margin-bottom: 30px;
        }
        .grafSection, .grafDetailSection{
            width: 100%;
        }
        .grafSection{
            margin-bottom: 30px;
        }
        .containerMenu .menuCover {
            width: 35%;
            float: left;
        }
        .containerMenu h5 {
            text-align: left;
        }
        .containerMenu.containerLunch{
            border: none;
            border-bottom: 2px solid rgba(0,0,0,.07);
            border-top: 2px solid rgba(0,0,0,.07);
            padding: 15px 0;
        }
        .containerMenu{
            margin-bottom: 15px;
            padding: 0;
        }
        .grafDetailSection .textCOntainer .giziText{
            text-align: center;
            margin: 0;
        }
        .grafDetailSection .textCOntainer{
            font-size: 18px;
        }
        .grafSection img {
            width: 50%;
        }
        .cardWeekly .containerTanggal{
            padding: 0;
        }
        .textContainer{
            width: 65%;
            float: right;
            padding: 8% 0 0;
        }
    }
</style>

<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Meal Planner</span>
                </div>
            </div>

            <div class="row sectionMarign">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-5" style="padding: 0;">
                            <div class="dropdown">
                                <button class="btn btn-outline-info dropdown-toggle" type="button" id="periodeDropDown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Per Minggu
                                    <i class="fas fa-sort-down" style="float: right;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu" aria-labelledby="periodeDropDown">
                                    <li><a class="dropdown-item" href="/planner-days">Beberapa Hari</a></li>
                                    <li><a class="dropdown-item" href="/planner-day">Per Hari</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-7" style="padding: 0;">
                            <div class="btn-group" role="group" id="containerMovePeriode">
                                <a href="/planner-week/{{$lastWeek}}"><button type="button" class="btn btn-outline-info"><i class="fas fa-chevron-left"></i></button></a>
                                <a href="/planner-week"><button type="button" class="btn btn-outline-info">Ke Hari Ini</button></a>
                                <a href="/planner-week/{{$nextWeek}}"><button type="button" class="btn btn-outline-info" style="border-left: 0;"><i class="fas fa-chevron-right"></i></button></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6" style="padding: 0;">
                    <a href="/generate">
                        <button class="btn btn-info" id="btnGenerate" type="button">
                            Generate
                        </button>
                    </a>
                </div>
            </div>
            
            
            @foreach ($listGenerate as $date => $value)
                @if($value['meta'] =='kosong')
                    <div class="row sectionMarign cardWeekly noHover">
                        <div class="col-sm-12 containerTanggal">
                            <h5><?php echo hari(str_replace("_","-",$date));?></h5>
                        </div>
                        <div clas="metaKosong">
                            empty data.
                        </div>
                @else
                    <div class="row sectionMarign cardWeekly">
                        <div class="col-sm-12 containerTanggal">
                            <h5><?php echo hari(str_replace("_","-",$date));?></h5>
                        </div>
                    <div class="col-sm-6 leftCardContainer">
                        <div class="grafDetailSection">
                            <div class="textCOntainer">
                                <div class="giziText">Total {{round($value['meta']['totalKalori'],0)}} kal</div>
                            </div>
                        </div>
                        <div class="grafSection" id="pie{{str_replace('-','_',$date)}}" align="center">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-4 containerMenu containerBreakfast">
                                <a class="linkResep" href="/resep/{{$value['breakfast']['id']}}">
                                    <h5>Breakfast</h5>
                                    <img class="menuCover" src="{{$value['breakfast']['cover']}}">
                                    <div class="textContainer">
                                        <h6>{{$value['breakfast']['nama_resep']}}</h6>
                                        <p>{{round($value['breakfast']['calories'],0)}} kal</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-4 containerMenu containerLunch">
                                <a class="linkResep" href="/resep/{{$value['lunch']['id']}}">
                                    <h5>Lunch</h5>
                                    <img class="menuCover" src="{{$value['lunch']['cover']}}">
                                    <div class="textContainer">
                                        <h6>{{$value['lunch']['nama_resep']}}</h6>
                                        <p>{{round($value['lunch']['calories'],0)}} kal</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-4 containerMenu containerDinner">
                                <a class="linkResep" href="/resep/{{$value['dinner']['id']}}">
                                    <h5>Dinner</h5>
                                    <img class="menuCover" src="{{$value['dinner']['cover']}}">
                                    <div class="textContainer">
                                        <h6>{{$value['dinner']['nama_resep']}}</h6>
                                        <p>{{round($value['dinner']['calories'],0)}} kal</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</main>

<script type="text/javascript">
// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

// Draw the chart and set the chart values
function drawChart() {
    <?php
        foreach($listGenerate as $date => $value){
            
            if($value['meta'] != "kosong"){
                echo '
                    var data'.str_replace("-","_",$date).' = google.visualization.arrayToDataTable([
                                ["Nutrisi", "Gram"],
                                ["Karbo '.round($value['meta']['totalCarbs'],0).' gram", '.round($value['meta']['totalCarbs'],0).'],
                                ["Lemak '.round($value['meta']['toatalFat'],0).' gram", '.round($value['meta']['toatalFat'],0).'],
                                ["Protein '.round($value['meta']['toatalProtein'],0).' gram", '.round($value['meta']['toatalProtein'],0).'],
                                ]);

                    var options'.str_replace("-","_",$date).' = {
                        legend: {position: "left"},
                        fontName: "gotham",
                        chartArea: {width:"100%",height:"100%"},
                        slices: {
                            0: { color: "#fcb524" },
                            1: { color: "#52c0bc" },
                            2: { color: "#976fe8" },
                        }
                    };

                    var chart'.str_replace("-","_",$date).' = new google.visualization.PieChart(document.getElementById("pie'.str_replace("-","_",$date).'"));
                    chart'.str_replace("-","_",$date).'.draw(data'.str_replace("-","_",$date).', options'.str_replace("-","_",$date).');
                ';
            }
        }
    ?>

}
$(window).resize(function(){
    drawChart();
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
 
	return "<div><b>".$hari_ini."</b>, ".date('d M Y',$time)."</div>";
 
}

?>