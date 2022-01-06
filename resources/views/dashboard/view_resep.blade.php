@include('dashboard.sidenav')

<meta name="csrf-token" content="{{ csrf_token() }}" />


<style>
    .titleMealPlan{
        font-size: 2.5rem;
        font-weight: 600;
    }
    .sectionMarign{
        margin: 30px 0;
    }
    .coverImage{
        width: 100%;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    .textJumlahSajian{
        font-weight: 400;
        color: grey;
    }
    #headerContainer{
        padding: 0 2em;
    }
    #detailContainer{
        padding: 0 3em;
    }
    .textLangkah, .textDetailInfo{
        color: black;
    }
    .textDetailInfo{
        margin-bottom: 0;
    }
    #containerDetailInfo{
        margin-bottom: 20px;
    }
    .likeMenu{
        font-size: 60px;
        color: pink;
        cursor: pointer;
    }
    #likeContainer{
        margin-right: 40px;
    }
    .saveMenu{
        font-size: 60px;
        cursor: pointer;
    }
    .detailSaveLike{
        margin-bottom: 20px;
    }
    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
        #headerContainer{
            padding: 0;
        }
        #detailContainer{
            padding: 0;
            margin-top: 60px;
        }
    }
</style>

<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Resep</span>
                </div>
            </div>

            <div class="row sectionMarign">
                <div class="col-sm-4" id="headerContainer">
                    <div class="row">
                        <div class="col-sm-12">
                            <img class="coverImage" src="{{$selectResep->cover}}" alt="{{$selectResep->judul_resep}}">
                        </div>
                        <div class="col-sm-12">
                            <h3>{{ucwords(strtolower($selectResep->judul_resep))}}</h3>
                            <h5 class="textJumlahSajian">Jumlah Sajian: {{$selectResep->yield}}</h5>
                            <br>
                            <h5 class="textJumlahSajian">Nutrisi Persajian</h5>
                            <table style="width: 100%;">
                                <tbody>
                                    <?php
                                        foreach (json_decode ($selectResep->digest) as $macros){
                                            if($macros->label == "Fat"){
                                                $fat = ($macros->total)/$selectResep->yield;
                                            }
                                            elseif($macros->label == "Protein"){
                                                $protein = ($macros->total)/$selectResep->yield;
                                            }
                                            elseif($macros->label == "Carbs"){
                                                $carbs = ($macros->total)/$selectResep->yield;
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td>Kalori</td>
                                        <td align="right">{{round(($selectResep->calories/round($selectResep->yield,0)),0)}} kcal</td>
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
                </div>
                <div class="col-sm-8" id="detailContainer">
                    <div class="row">
                        <div class="col-sm-12" style="display: flex;">
                            <div align="center" id="likeContainer">
                                @if ($idLike == null)
                                    <i onclick="likeMenu(<?php echo $selectResep->id;?>)" class="far fa-heart likeMenu"></i>
                                @else
                                    <i onclick="unlikeMenu(<?php echo $selectResep->id;?>)" class="fas fa-heart likeMenu"></i>
                                @endif
                                <div class="textDetailInfo detailSaveLike">{{$banyakLike}} orang</div>
                            </div>
                            <div align="center">
                                @if ($idSave == null)
                                    <i onclick="saveMenu(<?php echo $selectResep->id;?>)" class="far fa-bookmark saveMenu"></i>
                                @else
                                    <i onclick="unsaveMenu(<?php echo $selectResep->id;?>)" class="fas fa-bookmark saveMenu"></i>
                                @endif
                                <div class="textDetailInfo detailSaveLike">{{$banyakSave}} orang</div>
                            </div>
                        </div>
                        <div class="col-sm-12" id="containerDetailInfo">
                            <p class="textDetailInfo">
                                Oleh: {{ucwords(strtolower($selectResep->name))}}
                            </p>
                            <p class="textDetailInfo">
                                Post: {{date_format(date_create($selectResep->created_at),"d M Y")}}
                            </p>
                        </div>

                        <div class="col-sm-12">
                            <h4>Bahan </h4>
                            <p class="textBahan">
                                <ul>
                                    <?php
                                        $search = array("[","]",'"');
                                        $arrayBahan = $selectResep->bahanLines;
                                        $arrayBahan = str_replace($search,"",$arrayBahan);
                                        $arrayBahan = explode(",",$arrayBahan);
                                    ?>
                                    @foreach ( $arrayBahan as $bahan)
                                        <li>{{$bahan}}</li>
                                    @endforeach
                                </ul>
                            </p>
                        </div>

                        <div class="col-sm-12">
                            <h4>Langkah </h4>
                            <p class="textLangkah">
                                {{$selectResep->langkah}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>

<script>
    function likeMenu(x){
        var id = x ;
        
        $.ajaxSetup({ 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });

        $.ajax({
        url: "{{ url('/likeMenu') }}",
        type: "post",
        data:
        {id:id} ,
        async: true,
        timeout: 40000,
        success: function (response) {
            location.href = "/resep/"+id;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("error!");
        }
        });
    }
    function unlikeMenu(x){
        var id = x ;

        $.ajaxSetup({ 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });

        $.ajax({
        url: "{{ url('/unlikeMenu') }}",
        type: "post",
        data:
        {id:id} ,
        async: true,
        timeout: 40000,
        success: function (response) {
            location.href = "/resep/"+id;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("error!");
        }
        });
    }

    function saveMenu(x){
        var id = x ;
        
        $.ajaxSetup({ 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });

        $.ajax({
        url: "{{ url('/saveMenu') }}",
        type: "post",
        data:
        {id:id} ,
        async: true,
        timeout: 40000,
        success: function (response) {
            location.href = "/resep/"+id;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("error!");
        }
        });
    }
    function unsaveMenu(x){
        var id = x ;

        $.ajaxSetup({ 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });

        $.ajax({
        url: "{{ url('/unsaveMenu') }}",
        type: "post",
        data:
        {id:id} ,
        async: true,
        timeout: 40000,
        success: function (response) {
            location.href = "/resep/"+id;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("error!");
        }
        });
    }
</script>





@include('./footer')
