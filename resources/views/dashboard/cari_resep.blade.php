@include('dashboard.sidenav')

<script>
    $("#cariResep").addClass("active");
</script>

<style>
    .titleMealPlan{
        font-size: 2.5rem;
        font-weight: 600;
    }
    .sectionMarign{
        margin: 30px 0;
    }
    .resepContainer {
        columns: 4;
        column-gap: 1rem;
    }
    .resepContainer div {
        margin: 0 1rem 1rem 0;
        display: inline-block;
        width: 100%;
    }
    .card-title{
        word-break: break-word;
    }
    .searchContainer{
        display: flex;
        border: 1px solid rgba(0,0,0,.1);
        margin-bottom: 30px;
        border-radius: 20px;
        padding: 0 1em;
    }
    .searchContainer i{
        padding: 1em;
        transform: translateY(.3em);
    }
    #searchInput{
        box-shadow: unset;
        border: 0;
        padding: 1em;
        height: 100%;
    }
    .card{
        color: black;
    }
    .cardMenu:hover{
        box-shadow: 0px 0px 5px 4px #96d7c6 !important;
        /* border: 4px solid #96d7c6; */
    }
    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
        .resepContainer {
            columns: 2;
            column-gap: 1em;
        }
        .resepContainer div {
            margin: 0 0 1rem 0;
        }
        .card-title{
            font-size: 14px;
        }
        .card-body{
            padding: .5em;
            font-size: 12px;
        }
    }
</style>

<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Cari Resep</span>
                </div>
            </div>

            <div class="row sectionMarign">
                <div class="col-sm-12" style="padding: 0;">
                    <div class="searchContainer"> 
                        <i class="fa fa-search"></i> 
                        <input type="text" class="form-control" placeholder="ex. breakfast, vegan, nasi goreng" id="searchInput"> 
                    </div>
                </div>
                <div class="col-sm-12" style="padding: 0;">
                    <div class="resepContainer">
                        @foreach ($selectResep as $dataResep)
                        <?php 
                            $find = array('"','[',']',':','&','-','(',')','*',"1","2","3","4","5","6","7","8","9","0","'","\\","~","“");
                            $replace = array("");
                            $arraySearch = explode("/",str_replace(",","/",str_replace($find,$replace,$dataResep->cuisineType)));
                            $stringSearch = "";

                            foreach($arraySearch as $item){
                                $stringSearch = $stringSearch." s_".strtolower($item); 
                            }
                            
                            $arrayJudul = explode(" ",str_replace($find,$replace,$dataResep->judul_resep));
                            foreach($arrayJudul as $item){
                                $stringSearch = $stringSearch." s_".strtolower($item); 
                            }

                            $arrayHealtLabel = explode("/",str_replace(",","/",str_replace($find,$replace,$dataResep->healthLabels)));
                            foreach($arrayHealtLabel as $item){
                                $stringSearch = $stringSearch." s_".strtolower($item); 
                            }
                        ?>
                        <a href="/resep/{{$dataResep->id}}">
                            <div class="card cardMenu {{$stringSearch}}">
                                <img src="{{$dataResep->cover}}" class="card-img-top" alt="{{$dataResep->judul_resep}}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ucwords(strtolower($dataResep->judul_resep))}}</h5>
                                    <p class="card-text">Jumlah Sajian Resep: {{round($dataResep->yield,0)}}</p>
                                    <p class="card-text"  style="margin-bottom: 0">Nutrisi Persajian</p>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <?php
                                                foreach (json_decode ($dataResep->digest) as $macros){
                                                    if($macros->label == "Fat"){
                                                    $fat = ($macros->total)/$dataResep->yield;
                                                    }
                                                    elseif($macros->label == "Protein"){
                                                        $protein = ($macros->total)/$dataResep->yield;
                                                    }
                                                    elseif($macros->label == "Carbs"){
                                                        $carbs = ($macros->total)/$dataResep->yield;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>Kalori</td>
                                                <td align="right">{{round(($dataResep->calories/round($dataResep->yield,0)),0)}} kcal</td>
                                            </tr>
                                            <tr>
                                                <td>Karbo</td>
                                                <td align="right">{{round($carbs, 0)}} gram</td>
                                            </tr>
                                            <tr>
                                                <td>Protein</td>
                                                <td align="right">{{round($protein, 0)}} gram</td>
                                            </tr>
                                            <tr>
                                                <td>Lemak</td>
                                                <td align="right">{{round($fat, 0)}} gram</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>

<script>
    
$(document).ready(function(){
    $("#searchInput").on("input", function(){
        var searchInput = $(this).val();
        if(searchInput === 'undefined' ){
            $(".cardMenu").fadeIn(300);
        }
        else{
            $(".cardMenu").hide();
            // $(".cardMenu[class*=s_"+searchInput+"]").fadeIn(300);
            $(".cardMenu[class*=s_"+searchInput+"]").show();
        }
    });
});

</script>



@include('./footer')
