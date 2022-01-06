@include('dashboard.sidenav')

<script>
    $("#koleksiPage").addClass("active");
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
        columns: 3;
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
    #listOpsi{
        list-style: none;
        text-align: right;
        padding: 0 3em;
    }
    #listOpsi li{
        margin-bottom: 2em;
    }
    .pilihKategori{
        cursor: pointer;
        border-bottom: 1px solid white;
    }
    .pilihKategori.active, .pilihKategori:hover{
        border-bottom: 1px solid black;
    }
    #hrefBuatResep{
        margin-bottom: 30px;
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
        #listOpsi {
            list-style: none;
            text-align: left;
            padding: 0;
            display: block ruby;
            width: 100%;
        }
        #listOpsi li {
            margin-bottom: .3em;
            margin-right: 1em;
            padding: 0 5px;
        }
    }
</style>

<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Koleksi</span>
                </div>
            </div>

            <div class="row sectionMarign">
                <div class="col-sm-4"></div>
                <div class="col-sm-8" style="padding: 0;"  id="hrefBuatResep">
                    <a href="/buat-resep"><button type="button" class="btn btn-info">Buat Resep</button></a>
                </div>
                <div class="col-sm-4" style="padding: 0;">
                    <div>
                        <ul id="listOpsi">
                            <li class="pilihKategori active" kategori="all">All</li>
                            <li class="pilihKategori" kategori="list_resep">Resep Saya</li>
                            <li class="pilihKategori" kategori="like">Suka</li>
                            <li class="pilihKategori" kategori="save">Simpan</li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-8" style="padding: 0;">
                    <div class="resepContainer">
                        @foreach ($listResep as $dataResep)
                        <?php 
                            $kategori_list = "";

                            foreach($dataResep['kategori'] as $item){
                                $kategori_list = $kategori_list." cat_".strtolower($item); 
                            }
                        ?>
                        <a href="/resep/{{$dataResep['dataResep']->id}}">
                            <div class="card cardMenu {{$kategori_list}}">
                                <img src="{{$dataResep['dataResep']->cover}}" class="card-img-top" alt="{{$dataResep['dataResep']->judul_resep}}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ucwords(strtolower($dataResep['dataResep']->judul_resep))}}</h5>
                                    <p class="card-text">Jumlah Sajian Resep: {{round($dataResep['dataResep']->yield,0)}}</p>
                                    <p class="card-text"  style="margin-bottom: 0">Nutrisi Persajian</p>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <?php
                                                foreach (json_decode ($dataResep['dataResep']->digest) as $macros){
                                                    if($macros->label == "Fat"){
                                                    $fat = ($macros->total)/$dataResep['dataResep']->yield;
                                                    }
                                                    elseif($macros->label == "Protein"){
                                                        $protein = ($macros->total)/$dataResep['dataResep']->yield;
                                                    }
                                                    elseif($macros->label == "Carbs"){
                                                        $carbs = ($macros->total)/$dataResep['dataResep']->yield;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>Kalori</td>
                                                <td align="right">{{round(($dataResep['dataResep']->calories/round($dataResep['dataResep']->yield,0)),0)}} kcal</td>
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
    $(".pilihKategori").click(function(){
        var kategori = $(this).attr("kategori");
        if(kategori == "all"){
            $(".cardMenu").show();
            $(".pilihKategori").removeClass("active");
            $(this).addClass("active");
        }
        else{
            $(".cardMenu").hide();
            $(".cardMenu.cat_"+kategori).show();
            $(".pilihKategori").removeClass("active");
            $(this).addClass("active");
        }
    });
});

</script>



@include('./footer')
