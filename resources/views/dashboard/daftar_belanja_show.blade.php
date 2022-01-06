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
    #ingredCon{
        max-height: 50vh;
        overflow-y: scroll;
        padding: 1em 3em;
        box-shadow: 0px 0px 3px 1px rgba(0, 0, 0,.3) !important;
        border-radius: 20px;
        margin-top: 30px;
    }
    #ingredCon .row{
        margin: 0;
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
        #ingredCon{
            padding: 1em 2em;
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
                <div class="col-sm-12" style="padding: 0;">
                    <h5>Periode</h5>
                    <h2>{{$startDate}} - {{$endDate}}</h2>
                </div>

                <div class="col-sm-12" id="ingredCon">
                    <div class="row">
                        @foreach ( $listBelanja as $key => $value)
                        <div class="form-check col-sm-4">
                            @if ($value['qty'] == 0)
                                <input type="checkbox" class="form-check-input" id="{{$key}}">
                                <label class="form-check-label" for="{{$key}}">
                                    {{$key}}
                                </label>
                            @else   
                                <input type="checkbox" class="form-check-input" id="{{$key}}">
                                <label class="form-check-label" for="{{$key}}">
                                    {{($value['qty'])}} {{preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $value['measure'])) }} - {{$key}}
                                </label>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            

            
        </div>
    </div>
</main>

<script>

</script>




@include('./footer')