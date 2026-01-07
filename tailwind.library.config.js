/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./themes/default/views/library/**/*.php", "./themes/default/views/components/library/**/*.php"],
    theme: {
        extend: {
            colors: {
                primary: "#667eea",
                secondary: "#764ba2",
                accent: "#f093fb",
                background: "#0f0c29",
                surface: "rgba(255, 255, 255, 0.05)",
                surfaceHover: "rgba(255, 255, 255, 0.1)",
            },
            fontFamily: {
                sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'],
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'glass': 'linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.0))',
            },
            backdropBlur: {
                'xs': '2px',
            }
        },
    },
    plugins: [],
}
