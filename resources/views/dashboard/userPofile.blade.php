@include('dashboard.sidenav')

<meta name="csrf-token" content="{{ csrf_token() }}">


<script>
    $("#userProfilePage").addClass("active");
</script>


<style>
    .titleMealPlan{
        font-size: 2.5rem;
        font-weight: 600;
    }

    .contentBody{
        margin-top: 30px;
        padding: 5% 10%;
    }

    table{
        width: 100%;
    }
    
    .form-check {
        padding-left: 1.25rem;
        margin-right: 1.5em;
        display: inline-block;
    }

    label.form-label{
        width: 100%;
        font-weight: 600;
    }

    .form-check-label{
        cursor: pointer;
    }

    .formContainer{
        box-shadow: 0px 0px 3px 1px rgba(0,0,0,.2) !important;
        padding: 1em;
        border-radius: 10px;
    }

    #profileInfoContainer{
        margin-bottom: 30px;
    }

    .loadingBar{
        width: 20px;
        margin-left: 5px;
    }

    .biodataContainer{
        padding: 1em;
        margin-bottom: 3em;
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
        .contentBody {
            padding: 1em;
        }
        .biodataContainer table tr{
            border-bottom: 1px solid rgba(0,0,0,.2);
        }
        .biodataContainer table tr td, .biodataContainer table tr th{
            padding: .7em 0;
        }
        .biodataContainer table tr th{
            padding-right: 15px;
        }
    }
</style>

<main>
    <div class="site-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <span class="titleMealPlan">Track Profile</span>
                </div>
            </div>
            <div class="row contentBody">
                <div class="col-sm-12" style="padding: 0;">
                    @if ($meta == "kosong_all")
                    @elseif($meta == "kosong__userProfile")
                    <h5>Data Personal Information Telah terisi, tetapi data 
                        <b>Personal Information belum terisi</b>, 
                        harap isi data <b>Personal Information <a href="#profileInfoContainer">Di sini</a></b>
                    </h5>
                    @elseif ($meta == "kosong__users")
                    <h5>Data Personal Information Telah terisi, tetapi data 
                        <b>Basic Information belum terisi</b>, 
                        harap isi data <b>Basic Information <a href="#basicInfo">Di sini</a></b>
                    </h5>
                    @else
                    <h3>Biodata</h3>
                    <div class="biodataContainer">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Gender</th>
                                    <td>
                                        @if ($gender == "M")
                                            Pria
                                        @elseif ($gender == "F")
                                            Wanita
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>
                                        <?php echo hari(str_replace("_","-",$tglLahir));?> ({{$nutrisi['usia']['tahun']}} tahun {{$nutrisi['usia']['bulan']}} bulan {{$nutrisi['usia']['hari']}} hari)
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tinggi Badan</th>
                                    <td>{{$userProfileSelect->tinggi_badan}} cm</td>
                                </tr>
                                <tr>
                                    <th>Berat Badan</th>
                                    <td>{{$userProfileSelect->berat_badan}} kg</td>
                                </tr>
                                <tr>
                                    <th>Diet Preference</th>
                                    <td style="text-transform:capitalize">{{$userProfileSelect->orientasi_makanan}}</td>
                                </tr>
                                <tr>
                                    <th>Tingkat Aktifitas</th>
                                    <td>
                                        @if ($userProfileSelect->tingkat_aktivitas == 1)
                                            Level 1 - aktivitas rendah cenderung tidak berolahraga
                                        @elseif ($userProfileSelect->tingkat_aktivitas == 2)
                                            Level 2 - untuk orang yang sedikit aktif yang melakukan olahraga ringan 1–3 hari seminggu
                                        @elseif ($userProfileSelect->tingkat_aktivitas == 3)
                                            Level 3 - cukup aktif yang melakukan olahraga sedang 4–5 hari seminggu
                                        @elseif ($userProfileSelect->tingkat_aktivitas == 4)
                                            Level 4 - sangat aktif yang berolahraga keras 6–7 hari seminggu
                                        @elseif ($userProfileSelect->tingkat_aktivitas == 5)
                                            Level 5 - sangat aktif yang memiliki pekerjaan yang menuntut fisik atau memiliki rutinitas olahraga yang sangat menantang
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Health Laebl</th>
                                    <td style="text-transform:capitalize">{{$userProfileSelect->label}}</td>
                                </tr>
                                <tr>
                                    <th>Goal</th>
                                    <td style="text-transform:capitalize">{{$userProfileSelect->goal}}</td>
                                </tr>
                                <tr>
                                    <th>Kalori Harian</th>
                                    <td>{{round($nutrisi['tdeeBersih'],0)}} kcal</td>
                                </tr>
                                <tr>
                                    <th>Domisili</th>
                                    <td>{{$domisili}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="col-sm-12" style="padding: 0;" id="profileInfoContainer">
                    <h3>Personal Information</h3>
                    <form method="get" action="/update-profile" class="formContainer" id="formProfile">
                        @csrf 
                        <div class="input-group mb-3">
                            <label class="form-label">Tinggi Badan</label>
                            <input name="tinggiBadan" type="text" class="form-control" aria-label="tinggi Badan" id="tinggiBadan" placeholder="ex: 170" required="" pattern="[0-9]+" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;">
                            <span class="input-group-text">cm</span>
                        </div>

                        <div class="input-group mb-3">
                            <label class="form-label">Berat Badan</label>
                            <input name="beratBadan" type="text" class="form-control" aria-label="Berat Badan" id="beratBadan" placeholder="ex: 56" required="" pattern="[0-9]+" onkeypress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;">
                            <span class="input-group-text">kg</span>
                        </div>

                        <div class="input-group mb-3">
                            <label class="form-label">Tingkat Aktifitas dalam seminggu</label>
                            <select name="tingkatAktifitas" class="form-select" aria-label="Default select example">
                                <option value="1">Level 1 - aktivitas rendah cenderung tidak berolahraga</option>
                                <option value="2">Level 2 - untuk orang yang sedikit aktif yang melakukan olahraga ringan 1–3 hari seminggu</option>
                                <option value="3">Level 3 - cukup aktif yang melakukan olahraga sedang 4–5 hari seminggu</option>
                                <option value="4">Level 4 - sangat aktif yang berolahraga keras 6–7 hari seminggu</option>
                                <option value="5">Level 5 - sangat aktif yang memiliki pekerjaan yang menuntut fisik atau memiliki rutinitas olahraga yang sangat menantang</option>
                            </select>
                        </div>


                        <div class="input-group mb-3">
                            <label class="form-label">Diet preference</label>
                            <select name="preferance" class="form-select" aria-label="Default select example">
                                <option value="normal">Normal - makan daging dan bahan dari hewan</option>
                                <option value="vegetarian">Vegetarian - tidak makan daging tapi terkadang makan produk dari hewan seperti madu, telur, susu</option>
                                <option value="vegan">Vegan - tidak makan daging dan produk dari hewan seperti madu, telur, susu</option>
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <label class="form-label">Goal</label>
                            <select name="goal" class="form-select" aria-label="Default select example">
                                <option value="cutting">Cutting - Menurunkan berat badan (kalori defisit)</option>
                                <option value="maintain">Maintain - Stabilkan berat badan (kalori normal)</option>
                                <option value="bulking">Bulking - Menaikan berat badan (kalori surplus)</option>
                            </select>
                        </div>


                        <div class="input-group mb-3">
                            <label for="nama" class="form-label">Domisili</label>
                            <span class="input-group-text">Provinsi</span>
                            <select class="form-select" id="provinsi" name="provinsi" required>
                                <option value="pilih">--- pilih kota ---</option>
                                <?php
                                if ($err) {
                                    
                                } 
                                else {
                                    $provinsirespon = json_decode($response, true);
                                    $provinsi = $provinsirespon["rajaongkir"]["results"];
                                    
                                    foreach ($provinsi as $key => $value) {
                                        echo '<option value="'.$value["province_id"].'">'.$value["province"].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>  

                        <div class="input-group mb-3">
                            <span class="input-group-text">Kota <img class="loadingBar" src="assets/images/loading.gif"></span>
                            <select class="form-select" id="kota" disabled="" name="kota"></select>
                        </div>

                        <div class="helathLabelContanier">
                            <label>Health Label</label>
                            <div class="btn-group helathLabelCon" role="group" aria-label="Basic checkbox toggle button group">
                                <?php $cos = 0;?>
                                @foreach ( $healtLabel as $label => $value)
                                    <input type="checkbox" class="btn-check" id="btncheck{{$cos}}" name="healtLabel[]"value="{{$label}}" >
                                    <label class="btn btn-outline-primary" for="btncheck{{$cos}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$value}}">{{$label}}</label>
                                    <?php $cos++;?>
                                @endforeach    
                            </div>
                        </div>

                        <button class="btn btn-info btnSubmit" type="button" id="btnProfile">
                            Update
                        </button>
                    </form>
                </div>

                <div class="col-sm-12" style="padding: 0;" id="basicInfo">
                    <h3>Basic Information</h3>
                    <form method="POST" action="/update-basic" class="formContainer">
                        @csrf 
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <div class="form-check">
                                @if ($gender == "M")
                                    <input class="form-check-input" type="radio" value="M" id="gender_pria" name="gender" required checked>
                                @else
                                    <input class="form-check-input" type="radio" value="M" id="gender_pria" name="gender" required>
                                @endif
                                <label class="form-check-label" for="gender_pria">
                                    Pria
                                </label>
                            </div>
                            <div class="form-check">
                                @if ($gender == "F")
                                    <input class="form-check-input" type="radio" value="F" id="gender_wanita" name="gender" required checked>
                                @else
                                    <input class="form-check-input" type="radio" value="F" id="gender_wanita" name="gender" required>
                                @endif
                                <label class="form-check-label" for="gender_wanita">
                                    Wanita
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="birthdate">Tanggal Lahir</label>
                            <input name="tglLahir" id="tglLahir" type="date" class="form-control btn-outline-info datepicker" required value="{{$tglLahir}}" max="<?php echo date('Y-m-d');?>">
                        </div>    

                        <button class="btn btn-info btnSubmit" type="submit">
                            Update
                        </button>
                    </form>
                </div>
            </div>
            

            
        </div>
    </div>
</main>

<script>
$("#btnProfile").click(function(){
    var tinggiBadan = $("#tinggiBadan").val();
    var beratBadan = $("#beratBadan").val();
    var provinsi = $("#provinsi").val();
    var kota = $("#kota").val();

    if(tinggiBadan  === "" || beratBadan === ""){
        alert("semua belum terisi");
    }
    else{
        if(provinsi == "pilih" || kota == "pilih"){
            alert("semua belum terisi");
        }
        else{
            document.getElementById("formProfile").submit(); 
        }
    }
});

$(".loadingBar").css("opacity","0");

$('#provinsi').change(function() {
  if(this.value !== '') {
    var id = $("#provinsi").val();
    dataProvinsi = id;
    
    $.ajaxSetup({ 
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: "{{ url('/cariKota') }}",
      type: "post",
      data:
      {id:id} ,
      async: true,
      timeout: 40000,
      beforeSend: function() {
        $(".loadingBar").css("opacity","1");
        $('#kota').prop('disabled', true);
      },
      complete: function(){
        $(".loadingBar").css("opacity","0");
      },
      success: function (response) {
        $("#kota").html(response);
        $('#kota').prop('disabled', false);
        $("#provinsiTeks").val($( "#provinsi option:selected" ).text());
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error!");
      }
    });
  }
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
 
	return $hari_ini.", ".date('d M Y',$time);
 
}

?>
