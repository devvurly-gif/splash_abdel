<template>
    <div class="w-full">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-text-primary m-0 mb-2">Dashboard</h1>
            <p class="text-base text-text-secondary m-0">Welcome back! Here's what's happening.</p>
        </div>

        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-5 mb-6">
                <div class="bg-bg-elevated rounded-xl p-6 flex items-center gap-5 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white flex-shrink-0 bg-gradient-to-br from-[#667eea] to-[#764ba2]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold text-text-primary m-0 mb-1">$12,345</h3>
                        <p class="text-sm text-text-secondary m-0">Total Revenue</p>
                    </div>
                </div>

                <div class="bg-bg-elevated rounded-xl p-6 flex items-center gap-5 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white flex-shrink-0 bg-gradient-to-br from-[#f093fb] to-[#f5576c]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold text-text-primary m-0 mb-1">1,234</h3>
                        <p class="text-sm text-text-secondary m-0">Total Users</p>
                    </div>
                </div>

                <div class="bg-bg-elevated rounded-xl p-6 flex items-center gap-5 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white flex-shrink-0 bg-gradient-to-br from-[#4facfe] to-[#00f2fe]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold text-text-primary m-0 mb-1">567</h3>
                        <p class="text-sm text-text-secondary m-0">Orders</p>
                    </div>
                </div>

                <div class="bg-bg-elevated rounded-xl p-6 flex items-center gap-5 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white flex-shrink-0 bg-gradient-to-br from-[#43e97b] to-[#38f9d7]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold text-text-primary m-0 mb-1">89%</h3>
                        <p class="text-sm text-text-secondary m-0">Growth Rate</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(300px,1fr))] gap-6">
                <div class="bg-bg-elevated rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-semibold text-text-primary m-0 mb-5">Recent Activity</h2>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-accent-primary mt-1.5 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="text-sm text-text-primary m-0 mb-1">New user registered</p>
                                <span class="text-xs text-text-tertiary">2 minutes ago</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-accent-primary mt-1.5 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="text-sm text-text-primary m-0 mb-1">Order #1234 completed</p>
                                <span class="text-xs text-text-tertiary">15 minutes ago</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-accent-primary mt-1.5 flex-shrink-0"></div>
                            <div class="flex-1">
                                <p class="text-sm text-text-primary m-0 mb-1">Payment received</p>
                                <span class="text-xs text-text-tertiary">1 hour ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-bg-elevated rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-semibold text-text-primary m-0 mb-5">Quick Actions</h2>
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(120px,1fr))] gap-3">
                        <button class="flex flex-col items-center gap-2 p-4 bg-bg-secondary border-2 border-surface-border rounded-lg cursor-pointer transition-all duration-200 text-text-secondary hover:bg-accent-primary hover:border-accent-primary hover:text-white hover:-translate-y-0.5">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span class="text-xs font-medium">Add New</span>
                        </button>
                        <button class="flex flex-col items-center gap-2 p-4 bg-bg-secondary border-2 border-surface-border rounded-lg cursor-pointer transition-all duration-200 text-text-secondary hover:bg-accent-primary hover:border-accent-primary hover:text-white hover:-translate-y-0.5">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span class="text-xs font-medium">Export Data</span>
                        </button>
                        <button class="flex flex-col items-center gap-2 p-4 bg-bg-secondary border-2 border-surface-border rounded-lg cursor-pointer transition-all duration-200 text-text-secondary hover:bg-accent-primary hover:border-accent-primary hover:text-white hover:-translate-y-0.5">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
                            </svg>
                            <span class="text-xs font-medium">Settings</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Dashboard component logic can be added here
</script>

