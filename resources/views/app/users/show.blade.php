@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- Header Section --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center mb-2">
                        <h4 class="mb-0 me-2">Detail Pengguna</h4>
                        <span class="badge bg-label-success">Active</span>
                    </div>
                    <p class="mb-0 text-muted">Informasi lengkap mengenai akun dan struktur pengguna</p>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-2 mt-3 mt-md-0">
                    <a href="{{ route('users.index') }}" class="btn btn-label-secondary">
                        <i class="icon-base ti tabler-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row g-4">

                {{-- Profile Card with Avatar --}}
                <div class="col-xl-4 col-lg-5 col-md-5">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="user-avatar-section">
                                <div class="d-flex align-items-center flex-column">
                                    <div class="avatar avatar-xl mb-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary"
                                            style="font-size: 2rem;">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="user-info text-center">
                                        <h4 class="mb-2">{{ $user->name ?? '-' }}</h4>
                                        <div class="mb-3">
                                            @forelse ($user->roles as $role)
                                                <span class="badge bg-label-primary me-1">{{ ucfirst($role->name) }}</span>
                                            @empty
                                                <span class="badge bg-label-secondary">No Role</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="pb-2 border-bottom mb-3 mt-4">Details</h5>
                            <div class="info-container">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3">
                                        <span class="fw-medium me-2 text-heading">Email:</span>
                                        <span>{{ $user->email ?? '-' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2 text-heading">Gender:</span>
                                        <span>{{ $user->gender ?? '-' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2 text-heading">Bergabung:</span>
                                        <span>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-medium me-2 text-heading">Alamat:</span>
                                        <span>{{ $user->alamat ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions Card --}}
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Quick Actions</h5>
                            <div class="d-flex flex-column gap-2">
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-label-primary">
                                        <i class="icon-base ti tabler-edit me-2"></i> Edit Pengguna
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Organization & Additional Info --}}
                <div class="col-xl-8 col-lg-7 col-md-7">

                    {{-- Organization Card --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Struktur Organisasi</h5>
                            <span class="badge bg-label-info">Organizational Info</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-4 mb-md-0">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base ti tabler-briefcase text-success"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block mb-1">Unit</small>
                                            <h6 class="mb-0">{{ optional($user)->divisi ?? '-' }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="icon-base ti tabler-users text-warning"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block mb-1">Subunit</small>
                                            <h6 class="mb-0">{{ optional($user->subunit)->name ?? '-' }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Roles & Permissions Card --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Peran & Hak Akses</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-6">
                                @php
                                    $peranConfig = [
                                        1 => [
                                            'label' => 'Pegawai',
                                            'icon' => 'tabler-briefcase',
                                            'color' => 'primary',
                                            'description' => 'Karyawan tetap perusahaan',
                                        ],
                                        2 => [
                                            'label' => 'Keluarga Pegawai',
                                            'icon' => 'tabler-users',
                                            'color' => 'info',
                                            'description' => 'Anggota keluarga dari pegawai',
                                        ],
                                    ];
                                    $currentPeran = $peranConfig[$user->peran] ?? null;
                                @endphp

                                @if ($currentPeran)
                                    <div class="d-flex align-items-center mb-3 col-xl-12 col-sm-12">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-{{ $currentPeran['color'] }}">
                                                <i
                                                    class="icon-base ti {{ $currentPeran['icon'] }} text-{{ $currentPeran['color'] }}"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $currentPeran['label'] }}</h6>
                                            <small class="text-muted">{{ $currentPeran['description'] }}</small>
                                        </div>
                                        <span class="badge bg-label-{{ $currentPeran['color'] }}">
                                            {{ $currentPeran['label'] }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center py-4 col-xl-6 col-sm-6">
                                        <div class="avatar avatar-lg mb-3 mx-auto">
                                            <span class="avatar-initial rounded bg-label-secondary">
                                                <i class="icon-base ti tabler-user-question" style="font-size: 2rem;"></i>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-0">Pengguna tidak memiliki peran</p>
                                    </div>
                                @endif
                                @forelse ($user->roles as $role)
                                    <div class="d-flex align-items-center mb-3 col-xl-12 col-sm-12">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base ti tabler-shield-check"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ ucfirst($role->name) }}</h6>
                                            <small class="text-muted">Role ID: {{ $role->id }}</small>
                                        </div>
                                        <span class="badge bg-label-primary">Active</span>
                                    </div>
                                @empty
                                    <div class="text-center py-4 col-xl-6 col-sm-6">
                                        <div class="avatar avatar-lg mb-3 mx-auto">
                                            <span class="avatar-initial rounded bg-label-secondary">
                                                <i class="icon-base ti tabler-shield-off" style="font-size: 2rem;"></i>
                                            </span>
                                        </div>
                                        <p class="text-muted mb-0">Pengguna tidak memiliki hak akses</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Activity Timeline (Optional Enhancement) --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Timeline Aktivitas</h5>
                        </div>
                        <div class="card-body">
                            <ul class="timeline mb-0">
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Akun Dibuat</h6>
                                            <small
                                                class="text-muted">{{ $user->created_at ? $user->created_at->diffForHumans() : '-' }}</small>
                                        </div>
                                        <p class="mb-0">Pengguna terdaftar dalam sistem</p>
                                    </div>
                                </li>
                                @if ($user->updated_at && $user->updated_at != $user->created_at)
                                    <li class="timeline-item timeline-item-transparent">
                                        <span class="timeline-point timeline-point-info"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0">Terakhir Diperbarui</h6>
                                                <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0">Data pengguna diperbarui</p>
                                        </div>
                                    </li>
                                @endif
                                <li class="timeline-end-indicator">
                                    <i class="icon-base ti tabler-check"></i>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
