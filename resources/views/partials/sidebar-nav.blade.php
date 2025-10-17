{{-- resources/views/partials/sidebar-nav.blade.php - CREATE THIS FILE --}}
<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 border-r-2 border-indigo-600 text-indigo-700' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="{{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- Franchises -->
                <li>
                    <a href="{{ route('admin.franchises.index') }}"
                       class="{{ request()->routeIs('admin.franchises.*') ? 'bg-indigo-50 border-r-2 border-indigo-600 text-indigo-700' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="{{ request()->routeIs('admin.franchises.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Franchises
                        <span class="ml-auto w-6 min-w-max whitespace-nowrap rounded-full bg-white px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-gray-600 ring-1 ring-inset ring-gray-200">
                            {{ \App\Models\Franchise::count() }}
                        </span>
                    </a>
                </li>

                <!-- Students -->
                <li>
                    <a href="#"
                       class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="text-gray-400 group-hover:text-indigo-600 h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Students
                        <span class="ml-auto w-6 min-w-max whitespace-nowrap rounded-full bg-white px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-gray-600 ring-1 ring-inset ring-gray-200">
                            {{ \App\Models\Student::count() }}
                        </span>
                    </a>
                </li>

                <!-- Courses -->
                <li>
                    <a href="{{ route('admin.courses.index') }}"
                       class="{{ request()->routeIs('admin.courses.*') ? 'bg-indigo-50 border-r-2 border-indigo-600 text-indigo-700' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="{{ request()->routeIs('admin.courses.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        Courses
                        <span class="ml-auto w-6 min-w-max whitespace-nowrap rounded-full bg-white px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-gray-600 ring-1 ring-inset ring-gray-200">
                            {{ \App\Models\Course::count() }}
                        </span>
                    </a>
                </li>

                <!-- Exams -->
                <li>
                    <a href="#"
                       class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="text-gray-400 group-hover:text-indigo-600 h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        Exams
                    </a>
                </li>

                <!-- Certificates -->
                <li>
                    <a href="{{ route('admin.certificates.index') }}"
                       class="{{ request()->routeIs('admin.certificates.*') ? 'bg-indigo-50 border-r-2 border-indigo-600 text-indigo-700' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="{{ request()->routeIs('admin.certificates.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        Certificates
                        @if(\App\Models\Certificate::where('status', 'requested')->count() > 0)
                        <span class="ml-auto w-6 min-w-max whitespace-nowrap rounded-full bg-red-500 px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-white">
                            {{ \App\Models\Certificate::where('status', 'requested')->count() }}
                        </span>
                        @endif
                    </a>
                </li>

                <!-- Payments -->
                <li>
                    <a href="#"
                       class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="text-gray-400 group-hover:text-indigo-600 h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                        Payments
                    </a>
                </li>

                <!-- Reports -->
                <li>
                    <a href="#"
                       class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="text-gray-400 group-hover:text-indigo-600 h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Reports
                    </a>
                </li>
            </ul>
        </li>

        <!-- Settings Section -->
        <li class="mt-auto">
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">Settings</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <li>
                    <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-l-md p-2 text-sm leading-6 font-semibold">
                        <svg class="text-gray-400 group-hover:text-indigo-600 h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Profile
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
