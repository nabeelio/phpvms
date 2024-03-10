<header class="bg-blue-800 z-10 fixed w-screen" x-data="{ menuOpen: false }">
  <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
    <div class="flex lg:flex-1">
      <a href="#" class="-m-1.5 p-1.5">
        <span class="sr-only">SASva</span>
        <img class="h-8 w-auto" src="{{ public_asset('assets/sasva/media/sasvalogo_trans.svg') }}" alt="">
      </a>
    </div>
    <div class="flex lg:hidden">
      <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-white" @click="menuOpen = !menuOpen">
        <span class="sr-only">Open main menu</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>
    </div>
    @if(!Auth::check())
      <div class="hidden lg:flex lg:flex-1 lg:justify-end">
        <a href="#" class="text-sm inline-block text-white font-semibold px-2 py-1.5 hover:underline hover:text-gray-100">Sign In</a>
        <a href="#" class="text-sm inline-block text-black font-semibold px-2 py-1.5 bg-white rounded-md hover:bg-gray-100">Create an account</a>
      </div>
    @else
      <div class="hidden lg:flex lg:flex-1 lg:justify-end">
        <a href="#" class="text-sm inline-block text-white font-semibold px-2 py-1.5 hover:underline hover:text-gray-100">Sign Out</a>
        <a href="#" class="text-sm inline-block text-black font-semibold px-2 py-1.5 bg-white rounded-md hover:bg-gray-100">Profile</a>
      </div>
    @endif
  </nav>


  <!-- Mobile menu, show/hide based on menu open state. -->
  <div class="lg:hidden" role="dialog" aria-modal="true" :hidden="!menuOpen">
    <!-- Background backdrop, show/hide based on slide-over state. -->
    <div class="fixed inset-0 z-10"></div>
    <div class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-blue-800 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
      <div class="flex items-center justify-between">
        <a href="#" class="-m-1.5 p-1.5">
          <span class="sr-only">SASva</span>
          <img class="h-8 w-auto" src="{{ public_asset('assets/sasva/media/sasvalogo_trans.svg') }}" alt="">
        </a>
        <button type="button" class="-m-2.5 rounded-md p-2.5 text-white" @click="menuOpen = !menuOpen">
          <span class="sr-only">Close menu</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="mt-6 flow-root">
        <div class="-my-6 divide-y divide-gray-500/10">
          <div class="py-6">
            <a href="#" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-white">Sign In</a>
            <a href="#" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-white">Create an account</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>