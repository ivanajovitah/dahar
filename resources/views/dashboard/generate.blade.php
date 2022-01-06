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
    @media screen and (max-width: 810px) {
        .titleMealPlan{
            display: block;
        }
        .komposisiTipeContainer{
            display: block;
        }
        .cardKomposisi{
            width: 100%;
            border-radius: 20px !important;
            margin-bottom: 20px;
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
                <div class="col-sm-12 col-md-6" style="padding: 0;">
                    @if ($meta != "kosong")
                    <form method="POST" action="/generate-menu">
                        @csrf 
                        <h5>Dari tanggal:</h5>
                        <div class="input-group flex-nowrap dateContainer">
                            <span class="input-group-text btn-outline-info" id="calenderWrapping"><i class="far fa-calendar-alt"></i></span>
                            <input name="startDate" id="startDate" type="date" class="form-control btn-outline-info datepicker" required>
                        </div>
                        <h5>Sampai tanggal:</h5>
                        <div class="input-group flex-nowrap dateContainer">
                            <span class="input-group-text btn-outline-info" id="calenderWrapping"><i class="far fa-calendar-alt"></i></span>
                            <input name="endDate" id="endDate" type="date" class="form-control btn-outline-info datepicker" required>
                        </div>
                        <h5>Pilih Komposisi:</h5>
                        <input type="text" name="komposisi" id="komposisi" required hidden>
                        <div class="input-group flex-nowrap komposisiTipeContainer">
                            <div class="card cardKomposisi" komposisi="highKarbo">
                                <div class="card-body">
                                    <h5 class="card-title" align="center">Tingi Karbo</h5>
                                    <hr>
                                    <h5 class="card-sub-title">Karbo</h5>
                                    <h2 class="card-sub-title-value">50%</h2>
                                    <h5 class="card-sub-title">Protein</h5>
                                    <h2 class="card-sub-title-value">30%</h2>
                                    <h5 class="card-sub-title">Lemak</h5>
                                    <h2 class="card-sub-title-value">20%</h2>
                                </div>
                            </div>

                            <div class="card cardKomposisi" komposisi="moderateKarbo">
                                <div class="card-body">
                                    <h5 class="card-title" align="center">Sedang Karbo</h5>
                                    <hr>
                                    <h5 class="card-sub-title">Karbo</h5>
                                    <h2 class="card-sub-title-value">30%</h2>
                                    <h5 class="card-sub-title">Protein</h5>
                                    <h2 class="card-sub-title-value">35%</h2>
                                    <h5 class="card-sub-title">Lemak</h5>
                                    <h2 class="card-sub-title-value">35%</h2>
                                </div>
                            </div>

                            <div class="card cardKomposisi" komposisi="lowKarbo">
                                <div class="card-body">
                                    <h5 class="card-title" align="center">Rendah Karbo</h5>
                                    <hr>
                                    <h5 class="card-sub-title">Karbo</h5>
                                    <h2 class="card-sub-title-value">20%</h2>
                                    <h5 class="card-sub-title">Protein</h5>
                                    <h2 class="card-sub-title-value">40%</h2>
                                    <h5 class="card-sub-title">Lemak</h5>
                                    <h2 class="card-sub-title-value">40%</h2>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-info" id="btnGenerate" type="submit">
                            Generate
                        </button>
                    </form>
                    @else
                        <h5>
                            Harap isi data di menu <b>Track Porfile</b> terlebih dahulu atau melalui 
                            <a href="/track" style="text-decoration: underline !important;"><b>Link Ini</b></a>
                        </h5>
                    @endif
                </div>
            </div>
            

            
        </div>
    </div>
</main>

<script>
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0 so need to add 1 to make it 1!
    var yyyy = today.getFullYear();
    if(dd<10){
    dd='0'+dd
    } 
    if(mm<10){
    mm='0'+mm
    } 

    today = yyyy+'-'+mm+'-'+dd;
    document.getElementById("startDate").setAttribute("min", today); 
    document.getElementById("endDate").setAttribute("min", today); 
    $("#startDate").val(today); 


    $('#startDate').change(function() {
        var date = new Date($(this).val());

        var nextWeek = new Date(date.getFullYear(), date.getMonth(), date.getDate()+7);

        var day = ("0" + nextWeek.getDate()).slice(-2);
        var month = ("0" + (nextWeek.getMonth() + 1)).slice(-2);
        var nextWeekString = nextWeek.getFullYear()+"-"+(month)+"-"+(day);

        day = ("0" + date.getDate()).slice(-2);
        month = ("0" + (date.getMonth() + 1)).slice(-2);
        nextWeekString = date.getFullYear()+"-"+(month)+"-"+(day) ;

        document.getElementById("endDate").setAttribute("min", nextWeekString); 
    });

    $(".cardKomposisi").click(function(){
        var type = $(this).attr("komposisi");
        $(".cardKomposisi").removeClass("choose");
        $(this).addClass("choose");
        $("#komposisi").val(type);
    });
</script>




@include('./footer')