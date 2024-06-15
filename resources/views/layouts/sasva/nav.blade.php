<header class="flex flex-col bg-blue-800 z-10">
  <!-- TOP BAR HEADER -->
  <div id="header__top" class="flex h-20">
    <div class="container mx-auto flex flex-1 justify-between items-center">
      <div id="header__top-brand" class="relative flex flex-row justify-between items-center">
        <div id="header__top-brand-logo" class="flex">
          <a href="">
            <img class="inline-block h-16" src="https://dev.sasva.net/assets/sasva/media/sasvalogo_trans.svg" alt="">
          </a>
        </div>
      </div>
      @if(Auth::check())
        <div id="header__top-user" class="flex">
          <div class="cursor-pointer">
            <span class="text-base text-white">Hi,</span>
            <span class="text-base text-white font-medium">{{ Auth::user()->name_private }} | {{ Auth::user()->ident }}</span>
          </div>
        </div>
      @else
        <div id="header__top-login" class="flex">
          <div class="cursor-pointer">
            <span class="text-base text-white">TODO (SIGN IN / REGISTER)</span>
          </div>
        </div>
      @endif
    </div>
  </div>
  <!-- TOP BAR HEADER END -->
  
  <!-- BOTTOM BAR HEADER -->
  <div id="header__bottom" class="flex h-16 bg-blue-900">
    <div class="container mx-auto flex flex-1">
      <div class="flex justify-between items-center">
        <div class="flex">
          <ul class="flex">
            <li class="px-1">
              <a href="{{ route('frontend.dashboard.index') }}" class="text-base text-white font-medium rounded-sm px-3 py-2 hover:bg-white hover:text-gray-800">
                Dashboard
              </a>
            </li>
            <li class="px-1 relative" x-data="{ isOpen: false }" @mousedown.outside="isOpen = false">
              <a href="#" class="text-base font-medium rounded-sm px-3 py-2 hover:bg-white hover:text-gray-800" :class="isOpen ? 'bg-white text-gray-800' : 'text-white'" @click="isOpen = !isOpen">
                Operations
              </a>
              <div class="absolute left-0 z-10 mt-5 w-64 origin-top-right rounded-b-sm bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-show="isOpen" style="display:none;">
                <ul class="py-2">
                  <li class="hover:bg-gray-100 flex">
                    <a href="{{ route('frontend.flights.index') }}" class="px-4 py-2">Book Flight</a>
                  </li>
                  <li class="hover:bg-gray-100 flex">
                    <a href="{{ route('frontend.pireps.index') }}" class="px-4 py-2">PIREPs</a>
                  </li>
                  <li class="hover:bg-gray-100 flex">
                    <a href="" class="px-4 py-2">Live Map</a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="px-1 relative" x-data="{ isOpen: false }" @mousedown.outside="isOpen = false">
              <a href="#" class="text-base font-medium rounded-sm px-3 py-2 hover:bg-white hover:text-gray-800" :class="isOpen ? 'bg-white text-gray-800' : 'text-white'" @click="isOpen = !isOpen">
                Resources
              </a>
              <div class="absolute left-0 z-10 mt-5 w-64 origin-top-right rounded-b-sm bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-show="isOpen" style="display:none;">
                <ul class="py-2">
                  <li class="hover:bg-gray-100 flex">
                    <a href="" class="px-4 py-2">Downloads</a>
                  </li>
                  <li class="hover:bg-gray-100 flex">
                    <a href="" class="px-4 py-2">ACARS Client</a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!-- BOTTOM BAR HEADER END -->
</header>


