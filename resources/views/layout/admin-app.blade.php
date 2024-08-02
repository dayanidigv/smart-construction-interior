@extends('layout.app')
@section('content')


<div class="page-wrapper" id="main-wrapper" data-theme="blue_theme" data-layout="vertical" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <aside class="left-sidebar">
        <div>
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="/" class="text-nowrap logo-img">
                    <img src="{{ asset('images\logo\logo-2.png') }}" class="light-logo" width="50" alt="" />
                    <!-- <h1>Buildy</h1> -->
                </a>
                <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8 text-muted"></i>
                </div>
            </div>

            <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                <ul id="sidebarnav">

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Home</span>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link @if ($title == 'Index') active @endif" href="{{route('admin.index')}}"
                            aria-expanded="false">
                            <span>
                                <i class="ti ti-aperture"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                            <span class="d-flex">
                                <i class="ti ti-user-plus"></i>
                            </span>
                            <span class="hide-menu">Customer</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{route('admin.customer.add')}}" class="sidebar-link ">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">New</span>
                                </a>
                            </li>
                            <!-- <li class="sidebar-item">
                                <a href="{{route('admin.customer.list')}}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">List Customer</span>
                                </a>
                            </li> -->
                            <li class="sidebar-item">
                                <a href="{{route('admin.customer.list-all')}}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">List All</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Orders</span>
                    </li>



                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                            <span class="d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 9h.01" />
                                    <path d="M11 12h1v4h1" />
                                </svg>
                            </span>
                            <span class="hide-menu">Enquiries</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{route('enquiries.new',['role'=>'admin'])}}" class="sidebar-link ">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">New</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{route('enquiries.list',['role'=>'admin'])}}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">List</span>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                            <span class="d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-rocket">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3" />
                                    <path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3" />
                                    <path d="M15 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                </svg>
                            </span>
                            <span class="hide-menu">Order</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{route('admin.new.order')}}" class="sidebar-link ">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">New</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{route('admin.list.order')}}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">List</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-brand-google-photos">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M7.5 7c2.485 0 4.5 1.974 4.5 4.409v.591h-8.397a.61 .61 0 0 1 -.426 -.173a.585 .585 0 0 1 -.177 -.418c0 -2.435 2.015 -4.409 4.5 -4.409z" />
                                <path
                                    d="M16.5 17c-2.485 0 -4.5 -1.974 -4.5 -4.409v-.591h8.397c.333 0 .603 .265 .603 .591c0 2.435 -2.015 4.409 -4.5 4.409z" />
                                <path
                                    d="M7 16.5c0 -2.485 1.972 -4.5 4.405 -4.5h.595v8.392a.61 .61 0 0 1 -.173 .431a.584 .584 0 0 1 -.422 .177c-2.433 0 -4.405 -2.015 -4.405 -4.5z" />
                                <path
                                    d="M17 7.5c0 2.485 -1.972 4.5 -4.405 4.5h-.595v-8.397a.61 .61 0 0 1 .175 -.428a.584 .584 0 0 1 .42 -.175c2.433 0 4.405 2.015 4.405 4.5z" />
                            </svg>
                            <span class="hide-menu">Design</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item">
                                <a href="{{route('admin.new.design')}}" class="sidebar-link ">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">New</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{route('admin.list.design')}}" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">List</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{route('admin.report')}}" aria-expanded="false">
                            <span class="ti ti-file fs-5 fw-semibold"></span>
                            <span class="hide-menu">Report</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Others</span>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                            <span class="d-flex">
                                <i class="ti ti-box-multiple"></i>
                            </span>
                            <span class="hide-menu">Settings</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level">


                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{route('category.view',['role'=>'admin'])}}"
                                    aria-expanded="false">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 4h6v6h-6z" />
                                            <path d="M14 4h6v6h-6z" />
                                            <path d="M4 14h6v6h-6z" />
                                            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        </svg>
                                    </span>
                                    <span class="hide-menu">Category</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{route('admin.gallery')}}" aria-expanded="false">
                                    <span class="th-gallery fs-5 fw-semibold"></span>
                                    <span class="hide-menu">Gallery</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{route('admin.quantity-units')}}" aria-expanded="false">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-chart-dots-3">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 7m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M16 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                            <path d="M6 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                            <path d="M9 17l5 -1.5" />
                                            <path d="M6.5 8.5l7.81 5.37" />
                                            <path d="M7 7l8 -1" />
                                        </svg>
                                    </span>
                                    <span class="hide-menu">Quantity Units</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-bell fs-5"></i>
                                    </div>
                                    <span class="hide-menu">Reminder</span>
                                </a>
                                <ul aria-expanded="false" class="collapse two-level">
                                    <li class="sidebar-item">
                                        <a href="{{route('admin.reminder')}}" class="sidebar-link">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu">Set</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="{{route('admin.reminder.list')}}" class="sidebar-link">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu">List</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                                    <span class="d-flex">
                                        <i class="ti ti-user-plus fs-5"></i>
                                    </span>
                                    <span class="hide-menu">Users</span>
                                </a>
                                <ul aria-expanded="false" class="collapse two-level">
                                    <li class="sidebar-item">
                                        <a href="{{route('admin.add-user')}}" class="sidebar-link ">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu">New</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="{{route('admin.user.list')}}" class="sidebar-link ">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu">List</span>
                                        </a>
                                    </li>




                                </ul>
                            </li>

                        </ul>
                    </li>
            </nav>

            <div class="fixed-profile p-3 mx-4 mb-2 bg-light-info rounded sidebar-ad mt-3">
                <div class="hstack gap-3">
                    <div class="john-img">
                        <img src="/images/profile/user-1.jpg" class="rounded-circle" width="40" height="40" alt="" />
                    </div>
                    <div class="john-title">
                        <h6 class="mb-0 fs-4 fw-semibold">{{Auth::user()->name}}</h6>
                        <span class="fs-2">Admin</span>
                    </div>
                    <form action="{{route('logout')}}" class="ms-auto" method="post">
                        @csrf
                        <button class="border-0 bg-transparent text-primary " type="submit"
                            class="btn btn-outline-primary" aria-label="logout" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="logout">
                            <i class="ti ti-power fs-6"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div class="body-wrapper">

        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link sidebartoggler nav-icon-hover ms-n3" id="headerCollapse"
                            href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>

                <div class="d-block d-lg-none">
                    <img src="{{ asset('images\logo\logo-2.png') }}" class="light-logo" width="50" alt="" />
                    <!-- <h1>Buildy</h1> -->
                </div>

                <button class="navbar-toggler p-0 border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="p-2">
                        <i class="ti ti-dots fs-7"></i>
                    </span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <div class="d-flex align-items-center justify-content-between">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover notify-badge" href="javascript:void(0)" id="drop2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-bell-ringing"></i>
                                    @if (count($displayReminder) > 0)
                                    <span class="badge rounded-pill bg-danger fs-2">{{count($displayReminder)}}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                        <h5 class="mb-0 fs-5 fw-semibold">Reminder</h5>
                                        @if (count($displayReminder) != 0)
                                        <span
                                            class="badge bg-primary rounded-4 px-3 py-1 lh-sm">{{count($displayReminder)}}
                                            new</span>
                                        @endif
                                    </div>
                                    <div class="message-body" data-simplebar>
                                        @if (count($displayReminder) > 0)
                                        @for ($i = 0; $i < min(count($displayReminder), 7); $i++) <a
                                            href="{{route('admin.reminder.view',['encodedId' => base64_encode($displayReminder[$i]->id)])}}"
                                            class="py-6 px-7 d-flex align-items-center dropdown-item">
                                            <div class="w-75 d-inline-block v-middle">
                                                <h6 class="mb-1 fw-semibold">{{ $displayReminder[$i]->title }}</h6>
                                                <span class="d-block"
                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $displayReminder[$i]->description }}
                                                </span>
                                            </div>
                                            </a>
                                            @endfor
                                            @else
                                            <a href="javascript:void(0)"
                                                class="py-6 px-7 d-flex align-items-center dropdown-item">
                                                <div class="w-75 d-inline-block v-middle">
                                                    <h6 class="mb-1 fw-semibold">No Reminders</h6>
                                                </div>
                                            </a>

                                            @endif
                                    </div>
                                    @if (count($displayReminder) > 6)
                                    <div class="py-6 px-7 mb-1">
                                        <a href="{{route('admin.reminder.list')}}"><button
                                                class="btn btn-outline-primary w-100"> See All Reminders </button></a>
                                    </div>
                                    @endif
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <div class="user-profile-img">
                                            <img src="/images/profile/user-1.jpg" class="rounded-circle" width="35"
                                                height="35" alt="" />
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop1">
                                    <div class="profile-dropdown position-relative" data-simplebar>
                                        <div class="py-3 px-7 pb-0">
                                            <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                                        </div>
                                        <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                            <img src="/images/profile/user-1.jpg" class="rounded-circle" width="80"
                                                height="80" alt="" />
                                            <div class="ms-3">
                                                <h5 class="mb-1 fs-3">{{Auth::user()->name}}</h5>
                                                <span class="mb-1 d-block text-dark">Admin</span>
                                            </div>
                                        </div>
                                        <div class="d-grid py-4 px-7 pt-8">
                                            <form action="{{route('logout')}}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-primary">Log Out</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="container-fluid">

            <div class="card bg-white-info shadow-none position-relative overflow-hidden">
                <div class="row ">
                    <div class="col order-md-1 order-2">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item text-muted"><a href="/">{{ ucfirst($user->role)}}</a></li>
                                <li class="breadcrumb-item text-muted">{{ $menuTitle}}</li>
                                @if ($sectionName != null)
                                <li class="breadcrumb-item text-muted">{{$sectionName}}</li>
                                @endif
                                <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>{{ session('message') }}</strong>
            </div>
            @endif
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>{{ session('success') }}</strong>
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>{{ session('error') }}</strong>
            </div>
            @endif
            @yield('adminContent')
        </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>
    <!-- <div class="dark-transparent sidebartoggler"></div> -->
</div>

@endsection