@include('dashboard.sidenav')

<script>
    $("#daftarBelanja").addClass("active");
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
                    <span class="titleMealPlan">Daftar Belanja</span>
                </div>
            </div>
	
            <div class="row contentBody">
                <div class="col-sm-12 col-md-6" style="padding: 0;">
                    
                    <form method="POST" action="/daftar-belanja-show">
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
                        
                        <button class="btn btn-info" id="btnGenerate" type="submit">
                            Generate
                        </button>
                    </form>
                </div>

                <div class="col-sm-12 col-md-6">
                    
                </div>
            </div>
            

            
        </div>
    </div>
</main>

<script>

</script>




@include('./footer')