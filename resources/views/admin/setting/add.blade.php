@extends('admin.layout.app')

@section('title', 'Konfigurasi Parameter Aplikasi - NexoraPOS')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            
            <!-- Simple & Clean Page Header -->
            <div class="nexora-page-header-card mb-4">
                <div class="nexora-page-header-left">
                    <div class="nexora-page-header-icon">
                        <i class="fas fa-gear"></i>
                    </div>
                    <div class="nexora-page-header-meta">
                        <span class="nexora-page-header-eyebrow"><span class="dot"></span> PENGATURAN SISTEM &bull; APP CONFIGURATION</span>
                        <h3 class="nexora-page-header-title">Konfigurasi Aplikasi & Profil Publik</h3>
                        <p class="nexora-page-header-desc">Atur metode masuk login, teks pengumuman/newsletter, serta tautan media sosial resmi.</p>
                    </div>
                </div>
                <div class="nexora-page-header-right">
                    <a href="{{ url('admin-panel/settings/listing') }}" class="nexora-btn-back">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <form action="{{ url('admin-panel/settings/add') }}" method="POST" id="categoryForm">
                @csrf
                
                <!-- CARD 1: Konfigurasi Autentikasi & Konten -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-navy">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">SISTEM & AUTENTIKASI</span>
                            <h4 class="nexora-form-card-title">Metode Akses & Teks Aplikasi</h4>
                            <p class="nexora-form-card-subtitle">Pengaturan alur otentikasi login pengguna serta deskripsi resmi sistem.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Metode Login Utama</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-key input-icon"></i>
                                    <select name="login_method" id="login_method" class="form-control nexora-input">
                                        <option value="phone" {{ old('login_method', $settings->where('key', 'login_method')->first()->value ?? '') == 'phone' ? 'selected' : '' }}>Nomor Telepon / WhatsApp</option>
                                        <option value="email" {{ old('login_method', $settings->where('key', 'login_method')->first()->value ?? '') == 'email' ? 'selected' : '' }}>Alamat Email</option>
                                    </select>
                                </div>
                                <small class="nexora-input-help"><i class="fa fa-info-circle mr-1"></i>Metode utama yang disarankan saat kasir atau staf login.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Teks Newsletter / Pengumuman</label>
                                <div class="nexora-input-wrap">
                                    <i class="fas fa-bullhorn input-icon"></i>
                                    <input type="text" name="newsletter" class="form-control nexora-input" value="{{ $settings->where('key', 'newsletter')->first()->value ?? '' }}" placeholder="Pesan siaran atau promo di aplikasi...">
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="nexora-input-label">Tentang Kami (About Us)</label>
                                <textarea name="about_us" id="about_us" rows="4" class="form-control nexora-input" style="height: auto; padding: 12px 14px; border-radius: 12px;" placeholder="Profil singkat deskripsi platform atau perusahaan...">{{ $settings->where('key', 'about_us')->first()->value ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Tautan Media Sosial -->
                <div class="nexora-form-card mb-4">
                    <div class="nexora-form-card-head">
                        <div class="nexora-form-card-icon is-emerald">
                            <i class="fas fa-share-nodes"></i>
                        </div>
                        <div>
                            <span class="nexora-form-card-eyebrow">JARINGAN SOSIAL</span>
                            <h4 class="nexora-form-card-title">Tautan Akun Media Sosial</h4>
                            <p class="nexora-form-card-subtitle">Tautan resmi yang akan ditampilkan pada footer struk digital atau halaman publik.</p>
                        </div>
                    </div>

                    <div class="nexora-form-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Akun Facebook</label>
                                <div class="nexora-input-wrap">
                                    <i class="fab fa-facebook input-icon" style="color: #1877F2;"></i>
                                    <input type="text" name="facebook" class="form-control nexora-input" value="{{ $settings->where('key', 'facebook')->first()->value ?? '' }}" placeholder="https://facebook.com/namahalaman">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Akun Instagram</label>
                                <div class="nexora-input-wrap">
                                    <i class="fab fa-instagram input-icon" style="color: #E4405F;"></i>
                                    <input type="text" name="instagram" class="form-control nexora-input" value="{{ $settings->where('key', 'instagram')->first()->value ?? '' }}" placeholder="https://instagram.com/username">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Channel YouTube</label>
                                <div class="nexora-input-wrap">
                                    <i class="fab fa-youtube input-icon" style="color: #FF0000;"></i>
                                    <input type="text" name="youtube" class="form-control nexora-input" value="{{ $settings->where('key', 'youtube')->first()->value ?? '' }}" placeholder="https://youtube.com/@channel">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="nexora-input-label">Akun Twitter / X</label>
                                <div class="nexora-input-wrap">
                                    <i class="fab fa-x-twitter input-icon"></i>
                                    <input type="text" name="twitter" class="form-control nexora-input" value="{{ $settings->where('key', 'twitter')->first()->value ?? '' }}" placeholder="https://x.com/username">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="nexora-form-action-bar mb-5">
                    <a href="{{ url('admin-panel/settings/listing') }}" class="btn btn-secondary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">
                        <i class="fa fa-times mr-1"></i> Batalkan
                    </a>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 700; padding: 12px 32px; box-shadow: 0 4px 14px rgba(0, 88, 166, 0.35);">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
