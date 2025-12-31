/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.jsx",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Light Theme Colors
        light: {
          // Background colors
          'bg-primary': '#ffffff',
          'bg-secondary': '#f8fafc',
          'bg-tertiary': '#f1f5f9',
          'bg-elevated': '#ffffff',
          
          // Surface colors
          'surface-default': '#ffffff',
          'surface-hover': '#f8fafc',
          'surface-active': '#f1f5f9',
          'surface-border': '#e2e8f0',
          
          // Text colors
          'text-primary': '#0f172a',
          'text-secondary': '#475569',
          'text-tertiary': '#64748b',
          'text-disabled': '#cbd5e1',
          'text-inverse': '#ffffff',
          
          // Accent colors
          'accent-primary': '#6366f1',
          'accent-primary-hover': '#4f46e5',
          'accent-primary-light': '#e0e7ff',
          'accent-secondary': '#8b5cf6',
          'accent-success': '#10b981',
          'accent-warning': '#f59e0b',
          'accent-error': '#ef4444',
          'accent-info': '#3b82f6',
          
          // Border colors
          'border-default': '#e2e8f0',
          'border-hover': '#cbd5e1',
          'border-focus': '#6366f1',
        },
        
        // Dark Theme Colors
        dark: {
          // Background colors
          'bg-primary': '#0f172a',
          'bg-secondary': '#1e293b',
          'bg-tertiary': '#334155',
          'bg-elevated': '#1e293b',
          
          // Surface colors
          'surface-default': '#1e293b',
          'surface-hover': '#334155',
          'surface-active': '#475569',
          'surface-border': '#334155',
          
          // Text colors
          'text-primary': '#f8fafc',
          'text-secondary': '#cbd5e1',
          'text-tertiary': '#94a3b8',
          'text-disabled': '#64748b',
          'text-inverse': '#0f172a',
          
          // Accent colors
          'accent-primary': '#818cf8',
          'accent-primary-hover': '#a5b4fc',
          'accent-primary-light': '#312e81',
          'accent-secondary': '#a78bfa',
          'accent-success': '#34d399',
          'accent-warning': '#fbbf24',
          'accent-error': '#f87171',
          'accent-info': '#60a5fa',
          
          // Border colors
          'border-default': '#334155',
          'border-hover': '#475569',
          'border-focus': '#818cf8',
        },
        
        // Dynamic colors (using CSS variables for theme switching)
        // Background colors
        'bg-primary': 'var(--bg-primary)',
        'bg-secondary': 'var(--bg-secondary)',
        'bg-tertiary': 'var(--bg-tertiary)',
        'bg-elevated': 'var(--bg-elevated)',
        
        // Surface colors
        'surface': {
          DEFAULT: 'var(--surface-default)',
          hover: 'var(--surface-hover)',
          active: 'var(--surface-active)',
          border: 'var(--surface-border)',
        },
        
        // Text colors
        'text': {
          primary: 'var(--text-primary)',
          secondary: 'var(--text-secondary)',
          tertiary: 'var(--text-tertiary)',
          disabled: 'var(--text-disabled)',
          inverse: 'var(--text-inverse)',
        },
        
        // Accent colors
        'accent': {
          primary: 'var(--accent-primary)',
          'primary-hover': 'var(--accent-primary-hover)',
          'primary-light': 'var(--accent-primary-light)',
          secondary: 'var(--accent-secondary)',
          success: 'var(--accent-success)',
          warning: 'var(--accent-warning)',
          error: 'var(--accent-error)',
          info: 'var(--accent-info)',
        },
        
        // Border colors
        'border': {
          DEFAULT: 'var(--border-default)',
          hover: 'var(--border-hover)',
          focus: 'var(--border-focus)',
        },
      },
      boxShadow: {
        'sm': 'var(--shadow-sm)',
        'md': 'var(--shadow-md)',
        'lg': 'var(--shadow-lg)',
        'xl': 'var(--shadow-xl)',
      },
    },
  },
  plugins: [],
}

