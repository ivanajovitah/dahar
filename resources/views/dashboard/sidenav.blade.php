@include('./header')

<aside class="sidebar">
  <div class="toggle">
    <a href="#" class="burger js-menu-toggle" data-toggle="collapse" data-target="#main-navbar">
      <span></span>
    </a>
  </div>
  <div class="side-inner">

    <div class="profile">
      <img src="{{ asset('assets/images/logo_panjang_white.svg')}}" alt="" class="img-fluid">
      <h3 class="name">Hi, {{ucwords(strtolower(Auth::user()->name))}}</h3>
    </div>

    <div class="nav-menu">
      <ul>
        <li id="plannerPage"><a class="hoverEffect" href="/planner-week"><img src="{{ asset('assets/images/icon/calendar.png')}}">Meal Planner</a></li>
        <li id="daftarBelanja"><a class="hoverEffect" href="/daftar-belanja"><img src="{{ asset('assets/images/icon/groceries.png')}}">Daftar Belanja</a></li>
        <li id="generatePage"><a class="hoverEffect" href="/generate"><img src="{{ asset('assets/images/icon/generate.svg')}}">Generate Planner</a></li>
        <li id="cariResep"><a class="hoverEffect" href="/cari-resep"><img src="{{ asset('assets/images/icon/recipe.png')}}">Cari Resep</a></li>
        <li id="koleksiPage"><a class="hoverEffect" href="/koleksi"><img src="{{ asset('assets/images/icon/save.png')}}">Koleksi</a></li>
      </ul>
    </div>

    <div class="nav-menu" style="border: none">
      <ul>
        <li id="userProfilePage"><a class="hoverEffect" href="/track"><img src="{{ asset('assets/images/icon/user-profile.png')}}">Track Profile</a></li>
        <li><a class="hoverEffect" href="{{ route('profile.show') }}"><img src="{{ asset('assets/images/icon/account.png')}}">Account</a></li>
        <!-- <li><a class="hoverEffect" href="#"><img src="{{ asset('assets/images/icon/social-media.png')}}">Bagikan</a></li> -->
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();">
            <button>{{ __('Log Out') }}</button>
            </a>
          </form>
        </li>
      </ul>
      
    </div>

    
  </div>
</aside>
