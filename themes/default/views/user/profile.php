<?php

/**
 * User Profile Settings
 * Premium SaaS Glassmorphism UI with Tabs
 */

// Title and Layout handled by Controller and View class
?>

<div class="container mx-auto px-4 py-8 max-w-6xl" x-data="{ 
    activeTab: 'personal',
    saving: false,
    success: false,
    error: '',
    
    async submitForm() {
        this.saving = true;
        this.error = '';
        this.success = false;
        
        const form = document.getElementById('profileForm');
        const formData = new FormData(form);
        
        try {
            const response = await fetch('<?= app_base_url('/profile/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.success = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated',
                    text: data.message || 'Changes saved successfully',
                    background: '#0a0a0a',
                    color: '#fff',
                    confirmButtonColor: '#fff',
                    confirmButtonText: '<span class=&quot;text-black&quot;>Done</span>'
                });
            } else {
                this.error = data.message || 'Update failed';
                if (data.errors) {
                    this.error = Object.values(data.errors).flat().join(', ');
                }
                throw new Error(this.error);
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message,
                background: '#0a0a0a',
                color: '#fff'
            });
        } finally {
            this.saving = false;
        }
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-5">
            <div class="relative group">
                <div class="w-24 h-24 rounded-2xl overflow-hidden glass-card p-1 border-white/20 transition-transform group-hover:scale-105">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= htmlspecialchars(app_base_url($user['avatar'])) ?>" alt="Avatar" class="w-full h-full object-cover rounded-xl shadow-inner" id="userAvatar">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-4xl font-black text-white rounded-xl shadow-inner">
                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- File Input Hidden -->
                <input type="file" id="avatarInput" class="hidden" accept="image/*" onchange="uploadAvatar(this)">
                <button onclick="document.getElementById('avatarInput').click()" class="absolute -bottom-2 -right-2 w-10 h-10 rounded-full bg-primary text-background flex items-center justify-center shadow-lg border-4 border-background hover:scale-110 transition-transform cursor-pointer">
                    <i class="fas fa-camera text-sm"></i>
                </button>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white leading-tight flex items-center gap-3">
                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    <?php if (!empty($user['email_verified'])): ?>
                        <i class="fas fa-check-circle text-blue-400 text-lg" title="Verified"></i>
                    <?php endif; ?>
                </h1>
                <p class="text-gray-400 flex items-center gap-2 mt-1">
                    <span class="text-primary font-bold">@<?= htmlspecialchars($user['username']) ?></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
                    <span><?= htmlspecialchars($rank_data['rank'] ?? 'Beginner') ?></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-white/5 border border-white/10"><?= htmlspecialchars($user['role'] ?? 'User') ?></span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click="submitForm()" :disabled="saving" class="px-8 py-4 rounded-2xl bg-primary text-background font-black hover:scale-105 transition-transform flex items-center gap-3 shadow-xl shadow-white/5 disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="saving">
                    <i class="fas fa-spinner fa-spin"></i>
                </template>
                <template x-if="!saving">
                    <i class="fas fa-cloud-upload-alt"></i>
                </template>
                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
            </button>
        </div>
    </div>

    <!-- Main Content with Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-20">

        <!-- Tab Navigation (Sidebar Desktop, Top Mobile) -->
        <div class="lg:col-span-1">
            <div class="glass-card p-2 sticky top-8 flex flex-col gap-2">
                <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'bg-primary text-background shadow-lg shadow-white/20' : 'text-gray-400 hover:bg-white/5'" class="flex items-center gap-4 px-5 py-4 rounded-xl font-bold transition-all text-left">
                    <i class="fas fa-user-circle w-6 text-center"></i>
                    <span>Personal Info</span>
                </button>
                <button @click="activeTab = 'career'" :class="activeTab === 'career' ? 'bg-primary text-background shadow-lg shadow-white/20' : 'text-gray-400 hover:bg-white/5'" class="flex items-center gap-4 px-5 py-4 rounded-xl font-bold transition-all text-left">
                    <i class="fas fa-briefcase w-6 text-center"></i>
                    <span>Career & Profile</span>
                </button>
                <button @click="activeTab = 'social'" :class="activeTab === 'social' ? 'bg-primary text-background shadow-lg shadow-white/20' : 'text-gray-400 hover:bg-white/5'" class="flex items-center gap-4 px-5 py-4 rounded-xl font-bold transition-all text-left">
                    <i class="fas fa-share-alt w-6 text-center"></i>
                    <span>Social Links</span>
                </button>
                <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-primary text-background shadow-lg shadow-white/20' : 'text-gray-400 hover:bg-white/5'" class="flex items-center gap-4 px-5 py-4 rounded-xl font-bold transition-all text-left">
                    <i class="fas fa-cog w-6 text-center"></i>
                    <span>Preferences</span>
                </button>
                <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-primary text-background shadow-lg shadow-white/20' : 'text-gray-400 hover:bg-white/5'" class="flex items-center gap-4 px-5 py-4 rounded-xl font-bold transition-all text-left">
                    <i class="fas fa-shield-alt w-6 text-center"></i>
                    <span>Security</span>
                </button>

                <div class="mt-4 pt-4 border-t border-white/5">
                    <div class="px-5 pb-2 text-[10px] uppercase font-bold tracking-widest text-gray-600">Growth Progress</div>
                    <div class="px-5">
                        <div class="flex justify-between items-end mb-2">
                            <div class="text-xs font-bold text-white"><?= htmlspecialchars($rank_data['rank'] ?? 'Beginner') ?></div>
                            <div class="text-[10px] text-primary"><?= $rank_data['xp'] ?? 0 ?> / <?= $rank_data['next_rank_xp'] ?? 100 ?></div>
                        </div>
                        <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                            <?php $xpProgress = min(100, (($rank_data['xp'] ?? 0) / ($rank_data['next_rank_xp'] ?? 100)) * 100); ?>
                            <div class="h-full bg-gradient-to-r from-primary to-accent transition-all duration-1000" style="width: <?= $xpProgress ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="lg:col-span-3">
            <form id="profileForm" @submit.prevent="submitForm">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <!-- Personal Tab -->
                <div x-show="activeTab === 'personal'" x-transition class="space-y-8">
                    <div class="glass-card p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h2 class="text-2xl font-black text-white">Basic Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">First Name</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Last Name</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Display Username</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-bold">@</span>
                                    <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 pl-8 text-gray-500 cursor-not-allowed">
                                </div>
                                <p class="text-[10px] text-gray-600 pl-1 italic">Username is unique and permanent.</p>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Work Email</label>
                                <div class="relative">
                                    <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-gray-500 cursor-not-allowed">
                                    <i class="fas fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-700 text-xs"></i>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Phone Number</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+977 98XXXXXXXX" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Country / Location</label>
                                <input type="text" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>" placeholder="Kathmandu, Nepal" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Career Tab -->
                <div x-show="activeTab === 'career'" x-transition class="space-y-8">
                    <div class="glass-card p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400 text-xl">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h2 class="text-2xl font-black text-white">Professional Background</h2>
                        </div>

                        <div class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Professional Title</label>
                                    <input type="text" name="professional_title" value="<?= htmlspecialchars($user['professional_title'] ?? '') ?>" placeholder="e.g. Senior Civil Engineer" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Company / Institution</label>
                                    <input type="text" name="company" value="<?= htmlspecialchars($user['company'] ?? '') ?>" placeholder="e.g. Civil City Infrastructure" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Website URL</label>
                                <input type="text" name="website" value="<?= htmlspecialchars($user['website'] ?? '') ?>" placeholder="https://example.com" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Short Bio</label>
                                <textarea name="bio" rows="6" placeholder="Tell us about yourself, your skills, and what you're passionate about..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all resize-none"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Tab -->
                <div x-show="activeTab === 'social'" x-transition class="space-y-8">
                    <div class="glass-card p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 text-xl">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <h2 class="text-2xl font-black text-white">Digital Presence</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                            <?php
                            $platforms = [
                                'facebook' => ['icon' => 'fab fa-facebook-f', 'color' => 'bg-blue-600', 'placeholder' => 'fb.com/username'],
                                'twitter'  => ['icon' => 'fab fa-twitter', 'color' => 'bg-sky-500', 'placeholder' => 'twitter.com/handle'],
                                'linkedin' => ['icon' => 'fab fa-linkedin-in', 'color' => 'bg-blue-800', 'placeholder' => 'linkedin.com/in/user'],
                                'github'   => ['icon' => 'fab fa-github', 'color' => 'bg-gray-700', 'placeholder' => 'github.com/user'],
                                'instagram' => ['icon' => 'fab fa-instagram', 'color' => 'bg-pink-600', 'placeholder' => 'instagram.com/user'],
                                'telegram' => ['icon' => 'fab fa-telegram-plane', 'color' => 'bg-sky-600', 'placeholder' => 't.me/user']
                            ];
                            foreach ($platforms as $id => $info):
                                $val = $social_links[$id] ?? '';
                            ?>
                                <div class="group">
                                    <div class="flex items-center gap-4 mb-3">
                                        <div class="w-10 h-10 rounded-lg <?= $info['color'] ?> text-white flex items-center justify-center shadow-lg shadow-black/20 group-hover:scale-110 transition-transform">
                                            <i class="<?= $info['icon'] ?> text-lg"></i>
                                        </div>
                                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest"><?= ucfirst($id) ?></span>
                                    </div>
                                    <input type="text" name="social[<?= $id ?>]" value="<?= htmlspecialchars($val) ?>" placeholder="<?= $info['placeholder'] ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'" x-transition class="space-y-8">
                    <div class="glass-card p-8">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 text-xl">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <h2 class="text-2xl font-black text-white">App Preferences</h2>
                        </div>

                        <div class="space-y-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Primary Timezone</label>
                                    <select name="timezone" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all">
                                        <?php
                                        $timezones = DateTimeZone::listIdentifiers();
                                        $currentTimezone = $user['timezone'] ?? 'UTC';
                                        foreach ($timezones as $tz): ?>
                                            <option value="<?= $tz ?>" <?= $tz === $currentTimezone ? 'selected' : '' ?> class="bg-black text-white"><?= $tz ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Measurement System</label>
                                    <div class="flex gap-2 p-1.5 bg-white/5 rounded-2xl border border-white/10">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="measurement_system" value="metric" <?= ($user['measurement_system'] ?? 'metric') === 'metric' ? 'checked' : '' ?> class="hidden peer">
                                            <div class="py-3 text-center rounded-xl font-bold text-sm text-gray-400 peer-checked:bg-white peer-checked:text-black transition-all">Metric</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="measurement_system" value="imperial" <?= ($user['measurement_system'] ?? '') === 'imperial' ? 'checked' : '' ?> class="hidden peer">
                                            <div class="py-3 text-center rounded-xl font-bold text-sm text-gray-400 peer-checked:bg-white peer-checked:text-black transition-all">Imperial</div>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Study Context</label>
                                    <div class="flex gap-2 p-1.5 bg-white/5 rounded-2xl border border-white/10">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="study_mode" value="psc" <?= ($user['study_mode'] ?? 'psc') === 'psc' ? 'checked' : '' ?> class="hidden peer">
                                            <div class="py-3 text-center rounded-xl font-bold text-sm text-gray-400 peer-checked:bg-primary peer-checked:text-background transition-all">PSC Focused</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="study_mode" value="world" <?= ($user['study_mode'] ?? '') === 'world' ? 'checked' : '' ?> class="hidden peer">
                                            <div class="py-3 text-center rounded-xl font-bold text-sm text-gray-400 peer-checked:bg-primary peer-checked:text-background transition-all">Global Field</div>
                                        </label>
                                    </div>
                                    <p class="text-[10px] text-gray-600 pl-1">Changes calculations context to regional or international standards.</p>
                                </div>
                            </div>

                            <div class="group flex items-center justify-between p-6 bg-white/5 rounded-2xl border border-white/10 hover:border-primary/20 transition-all">
                                <div>
                                    <h4 class="font-bold text-white mb-1">Email Notifications</h4>
                                    <p class="text-xs text-gray-500">Receive weekly digests, performance reports, and security alerts.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_notifications" <?= ($user['email_notifications'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-14 h-8 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div x-show="activeTab === 'security'" x-transition class="space-y-8">
                    <!-- 2FA Card -->
                    <div class="glass-card p-8 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>

                        <div class="flex flex-col md:flex-row gap-8 items-start justify-between relative z-10">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                                        <i class="fas fa-fingerprint"></i>
                                    </div>
                                    <h2 class="text-2xl font-black text-white">Authentication</h2>
                                </div>
                                <p class="text-gray-400 mb-6 leading-relaxed">
                                    Secure your account with multi-factor authentication. By enabling this, you'll need a unique code from your authenticator app to log in, significantly increasing your account security.
                                </p>

                                <div class="flex items-center gap-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full <?= $two_factor_status['enabled'] ? 'bg-green-500 shadow-lg shadow-green-500/50 pulse-lite' : 'bg-gray-700' ?>"></div>
                                        <span class="text-sm font-bold <?= $two_factor_status['enabled'] ? 'text-green-400' : 'text-gray-500' ?>">
                                            <?= $two_factor_status['enabled'] ? '2FA Active' : '2FA Inactive' ?>
                                        </span>
                                    </div>
                                    <button type="button" onclick="toggle2FA()" class="px-6 py-2 rounded-xl font-bold text-sm bg-white/5 border border-white/10 hover:bg-white hover:text-black transition-all">
                                        <?= $two_factor_status['enabled'] ? 'Configure Recovery' : 'Setup 2FA Now' ?>
                                    </button>
                                </div>
                            </div>

                            <?php if ($two_factor_status['enabled']): ?>
                                <div class="w-full md:w-auto p-4 bg-white/5 rounded-2xl border border-white/10 text-center">
                                    <i class="fas fa-shield-check text-4xl text-primary mb-2"></i>
                                    <div class="text-[10px] uppercase font-black text-gray-500">Security Score</div>
                                    <div class="text-2xl font-black text-white">100%</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="glass-card p-8 border-accent/20">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent text-xl">
                                <i class="fas fa-key"></i>
                            </div>
                            <h2 class="text-2xl font-black text-white">Account Password</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                            <div class="space-y-4">
                                <p class="text-xs text-gray-500">Changing your password will log out all other active sessions for this account.</p>
                                <button type="button" @click="Swal.fire({
                                    title: 'Change Password',
                                    html: `<input id='old-pw' type='password' class='swal2-input' placeholder='Current Password'>
                                           <input id='new-pw' type='password' class='swal2-input' placeholder='New Password'>
                                           <input id='con-pw' type='password' class='swal2-input' placeholder='Confirm New Password'>`,
                                    background: '#0a0a0a',
                                    color: '#fff',
                                    confirmButtonText: 'Update Security',
                                    confirmButtonColor: '#fff',
                                    preConfirm: () => {
                                        const oldP = Swal.getPopup().querySelector('#old-pw').value;
                                        const newP = Swal.getPopup().querySelector('#new-pw').value;
                                        const conP = Swal.getPopup().querySelector('#con-pw').value;
                                        if (!oldP || !newP || !conP) {
                                            Swal.showValidationMessage('Please fill all fields');
                                        }
                                        if (newP !== conP) {
                                            Swal.showValidationMessage('Passwords do not match');
                                        }
                                        return { oldP, newP, conP };
                                    }
                                }).then((res) => {
                                    if(res.isConfirmed) submitPasswordChange(res.value);
                                })" class="w-full py-4 rounded-xl border border-accent/30 text-accent font-bold hover:bg-accent/5 transition-all">
                                    Change Security Password
                                </button>
                            </div>

                            <div class="p-6 bg-accent/5 rounded-2xl border border-accent/10">
                                <div class="flex items-center gap-3 text-accent mb-2">
                                    <i class="fas fa-exclamation-triangle text-xs"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Delete Account</span>
                                </div>
                                <p class="text-[10px] text-gray-500 mb-4 font-bold">Permanently remove all data, calculations, and progress. This cannot be undone.</p>
                                <button type="button" onclick="confirmDeletion()" class="text-[10px] font-bold text-gray-400 hover:text-accent underline underline-offset-4">Destroy account forever</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Extra Styles for Pulse and Animations -->
<style>
    @keyframes pulse-lite {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
    }

    .pulse-lite {
        animation: pulse-lite 2s infinite ease-in-out;
    }

    .bg-primary {
        background-color: #fff !important;
    }

    .text-primary {
        color: #fff !important;
    }

    .border-primary {
        border-color: #fff !important;
    }

    .text-background {
        color: #000 !important;
    }

    /* Override default Tailwind for theme */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
    }
</style>

<!-- Scripts -->
<script>
    async function uploadAvatar(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('csrf_token', '<?= csrf_token() ?>');

        try {
            Swal.fire({
                title: 'Uploading...',
                didOpen: () => Swal.showLoading(),
                background: '#0a0a0a',
                color: '#fff'
            });

            const response = await fetch('<?= app_base_url('/profile/avatar') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                document.getElementById('userAvatar').src = '<?= app_base_url('') ?>' + data.avatar_path;
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Avatar updated!',
                    timer: 1500,
                    background: '#0a0a0a',
                    color: '#fff'
                });
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: e.message,
                background: '#0a0a0a',
                color: '#fff'
            });
        }
    }

    async function submitPasswordChange(payload) {
        const formData = new FormData();
        formData.append('csrf_token', '<?= csrf_token() ?>');
        formData.append('old_password', payload.oldP);
        formData.append('new_password', payload.newP);
        formData.append('confirm_password', payload.conP);

        try {
            const response = await fetch('<?= app_base_url('/profile/password') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Security Updated',
                    text: 'Password changed successfully',
                    background: '#0a0a0a',
                    color: '#fff'
                });
            } else {
                throw new Error(data.message || 'Password update failed');
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message,
                background: '#0a0a0a',
                color: '#fff'
            });
        }
    }

    function confirmDeletion() {
        Swal.fire({
            title: 'Are you absolutely sure?',
            text: 'Type "DELETE" to confirm your account closure.',
            input: 'text',
            inputValidator: (val) => val !== 'DELETE' && 'You must type DELETE',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Destroy Account',
            background: '#0a0a0a',
            color: '#fff'
        }).then(async (res) => {
            if (res.isConfirmed) {
                // Need password for final verification
                const {
                    value: password
                } = await Swal.fire({
                    title: 'Final Identity Check',
                    input: 'password',
                    inputPlaceholder: 'Enter your password',
                    background: '#0a0a0a',
                    color: '#fff'
                });

                if (password) {
                    const formData = new FormData();
                    formData.append('csrf_token', '<?= csrf_token() ?>');
                    formData.append('password', password);
                    formData.append('confirmation', 'DELETE');

                    const response = await fetch('<?= app_base_url('/profile/delete') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        window.location.href = '<?= app_base_url('/') ?>';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: data.message,
                            background: '#0a0a0a',
                            color: '#fff'
                        });
                    }
                }
            }
        });
    }

    function toggle2FA() {
        window.location.href = '<?= app_base_url('/user/security') ?>';
    }
</script>