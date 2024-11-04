<x-guest-layout>
    @section('meta_title', View::getSection('title') . ' | ' . App\Facades\DomainFacade::getDomainSettings()->name)
    @section('meta_description', View::getSection('message') . ' - ' . View::getSection('code'))
    <div class="page-content">
        <div class="container text-center ">
            @if (View::getSection('code') === '404')
                <img src="/assets/images/svgs/404.svg" alt="404" class="w-30 mb-6"/>
                <h1 class="h3 mb-3 font-weight-bold">{{ __('Sorry, an error has occured, Requested Page not found!') }}</h1>
                <p class="h5 font-weight-normal mb-7 leading-normal">{{ __('You may have mistyped the address or the page may have moved.') }}</p>
            @else
                <h1 class="h3  mb-3 font-weight-bold">
                    @yield('message') | @yield('code')
                </h1>
                <p class="h5 font-weight-normal mb-7 leading-normal">
                    @if(Auth::user() instanceof \Brackets\CraftablePro\Models\CraftableProUser)
                        {{ $exception->getMessage() }}
                    @else
                        {{ __('Please contact the administrator or try again later.') }}
                    @endif
                </p>
            @endif
            <a class="btn btn-primary" href="/">
                <i class="fe fe-arrow-left-circle m-1"></i>
                {{ __('Back to Home') }}
            </a>
        </div>
    </div>
</x-guest-layout>
