<header class="bg-white shadow-sm px-4 md:px-8 h-20 flex items-center justify-between">

    <div>

        <h2 class="text-xl md:text-2xl font-bold">

            Dashboard

        </h2>

    </div>

    <div class="flex items-center gap-3 md:gap-6">

        <span class="text-gray-600 hidden md:block">

            {{ Auth::user()->name }}

        </span>

        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button
                class="bg-red-500 hover:bg-red-600 text-white px-3 md:px-5 py-2 rounded-xl">

                Logout

            </button>

        </form>

    </div>

</header>