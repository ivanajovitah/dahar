@include('dashboard.sidenav')

<!-- Select2 CSS --> 
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> 
<!-- Select2 JS --> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


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
    .bahanContainer{
        margin-top: 30px;
    }
    .rowBahan{
        display: flex;
        width: 100%;
        margin: 10px 0;
    }
    .bahanQTY{
        width: 20%;
        display: flex;
        margin-right: 1%;
    }
    .select2-container{
        width: 80% !important;
    }
    .namaPlace{
        margin-right: 5px;
    }
    .but_read{
        height: 28px;
        padding: 0 5px;
        margin-left: 1%;
    }
    #submit{
        margin-top: 30px;
    }
    .helathLabelContanier{
        margin-top: 30px;
    }
    .helathLabelCon{
        display: inline-block;
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
        .rowBahan {
            display: inline-block;
        }
        .bahanQTY {
            width: 50%;
            display: flex;
            margin-right: 0;
            margin-bottom: 5px;
        }
        .namaPlace {
            margin-right: 0;
            margin-bottom: 5px;
        }
        .select2-container {
            width: 90% !important;
        }
        .bahanContainer {
            margin-top: 30px;
            margin-bottom: 30px;
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

            <form action="/saveMake_resep" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
           
            <div class="row sectionMarign">
                <div class="col-sm-12">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        Bahan, Jenis Makanan, dan Health Label wajib diisi
                    </div>
                @endif
                </div>
                <div class="col-sm-4" id="headerContainer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div>
                                <img id="preview-image-before-upload" src="https://www.riobeauty.co.uk/images/product_image_not_found.gif"
                                    alt="preview image" style="width: 100%;margin-bottom: 10px;">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="file" name="image" placeholder="Choose image" id="image" required>
                                        @error('image')
                                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">Judul Resep</label>
                            <input type="text" class="form-control" name="judul" required>

                            <label class="form-label">Jumlah Sajian</label>
                            <input type="number" class="form-control" name="jumlah_sajian" required>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8" id="detailContainer">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>Bahan</h4>
                            <select class="form-select optionBahan selUser" aria-label="Default select example" name='bahan' id="selcetUSER_1">
                                @foreach($list_komposisi as $bahan)
                                <option value="{{$bahan->id}}" energi="{{$bahan->energi}}" karbo="{{$bahan->karbo}}" protein="{{$bahan->protein}}" lemak="{{$bahan->lemak}}" makanan="{{$bahan->makanan}}">{{$bahan->nama_komposisi}}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-info but_read"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="col-sm-12">
                            <div class="bahanContainer">
                                
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <h4>Langkah </h4>
                            <div class="form-floating">
                                <textarea required class="form-control" placeholder="langkah..." id="floatingTextarea2" style="height: 100px" name="langkah"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 helathLabelContanier">
                            <h4>Jenis Makanan</h4>
                            <small><i>minimal pilih 1</i></small><br><br>
                            <div class="btn-group helathLabelCon" role="group" aria-label="Basic checkbox toggle button group" required>
                                <input type="checkbox" class="btn-check" id="btnBreakfast" name="cuisineType[]" value="breakfast">
                                <label class="btn btn-outline-primary" for="btnBreakfast">Breakfast</label>

                                <input type="checkbox" class="btn-check" id="btncheck2" name="cuisineType[]"value="lunch">
                                <label class="btn btn-outline-primary" for="btncheck2">Lunch</label>

                                <input type="checkbox" class="btn-check" id="btncheck3" name="cuisineType[]"value="dinner">
                                <label class="btn btn-outline-primary" for="btncheck3">Dinner</label>
                            </div>
                            
                        </div>
                        <div class="col-sm-12 helathLabelContanier">
                            <h4>Health Label </h4>
                            <small><i>minimal pilih 1</i></small><br><br>
                            <div class="btn-group helathLabelCon" role="group" aria-label="Basic checkbox toggle button group">
                                <?php $cos = 0;?>
                                @foreach ( $healtLabel as $label => $value)
                                    <input type="checkbox" class="btn-check" id="btncheckHealth{{$cos}}" name="healtLabel[]"value="{{$healtLabel[$label]}}" >
                                    <label class="btn btn-outline-primary" for="btncheckHealth{{$cos}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$value}}">{{$label}}</label>
                                    <?php $cos++;?>
                                @endforeach    
                            </div>
                            <button type="submit" class="btn btn-info" id="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            
        </div>
    </div>
</main>

<script>

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    })


    // Initialize select2
    $("#selcetUSER_1").select2();

    var counter= 0;

    // var

    // Read selected option
    $('.but_read').click(function(){
        counter++;
        var nama = $('#selcetUSER_1 option:selected').text();
        var energi = $('#selcetUSER_1 option:selected').attr("energi");
        var protein = $('#selcetUSER_1 option:selected').attr("protein");
        var karbo = $('#selcetUSER_1 option:selected').attr("karbo");
        var lemak = $('#selcetUSER_1 option:selected').attr("lemak");
        var lemak = $('#selcetUSER_1 option:selected').attr("lemak");
        var makanan = $('#selcetUSER_1 option:selected').attr("makanan");
        var selectBahan = '<div class="rowBahan" id="containerBahan_'+counter+'">'+
                                    '<input type="number" class="form-control bahanQTY" name="bahan_qty[]" id="bahan_'+counter+'" placeholder="qty (gram)" required value="1">'+
                                    '<input type="text" class="form-control namaPlace"cid="nama_'+counter+'" value="'+nama+'" name="nama[]" readonly>'+
                                    '<input type="text" class="form-control" id="energi_'+counter+'" value="'+energi+'" name="energi[]" readonly hidden>'+
                                    '<input type="text" class="form-control" id="protein_'+counter+'" value="'+protein+'" name="protein[]" readonly hidden>'+
                                    '<input type="text" class="form-control" id="karbo_'+counter+'" value="'+karbo+'" name="karbo[]" readonly hidden>'+
                                    '<input type="text" class="form-control" id="lemak_'+counter+'" value="'+lemak+'" name="lemak[]" readonly hidden>'+
                                    '<input type="text" class="form-control" id="tipe_'+counter+'" value="'+makanan+'" name="tipe[]" readonly hidden>'+
                                    '<button type="button" class="btn btn-info deletBtn" index="'+counter+'"><i class="fas fa-trash-alt"></i></button>'+
                                '</div>';


        $( ".bahanContainer" ).append(selectBahan);

        $(".deletBtn").click(function(){
            var index = $(this).attr("index");
                $("#containerBahan_"+index).remove();
            });
        });

        $(document).ready(function (e) {
    
    
    $('#image').change(function(){
            
    let reader = new FileReader();

    reader.onload = (e) => { 

        $('#preview-image-before-upload').attr('src', e.target.result); 
    }

    reader.readAsDataURL(this.files[0]); 
    
    });
    
    });



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
