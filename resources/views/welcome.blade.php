<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TASKFLOW</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #090d16;
                color: #ffffff;
                font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                overflow: hidden;
                position: relative;
            }

            /* Ambient background glow */
            .glow-bg {
                position: absolute;
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(168, 85, 247, 0.12) 35%, transparent 70%);
                border-radius: 50%;
                filter: blur(80px);
                pointer-events: none;
                animation: pulseGlow 8s ease-in-out infinite alternate;
            }

            .grid-overlay {
                position: absolute;
                inset: 0;
                background-image: 
                    linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
                background-size: 40px 40px;
                mask-image: radial-gradient(ellipse 60% 50% at 50% 50%, #000 70%, transparent 100%);
                -webkit-mask-image: radial-gradient(ellipse 60% 50% at 50% 50%, #000 70%, transparent 100%);
                pointer-events: none;
            }

            .container {
                position: relative;
                z-index: 10;
                text-align: center;
                padding: 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2rem;
            }

            .title {
                font-size: clamp(3rem, 10vw, 6.5rem);
                font-weight: 900;
                letter-spacing: 0.18em;
                text-indent: 0.18em;
                text-transform: uppercase;
                background: linear-gradient(135deg, #ffffff 20%, #cbd5e1 50%, #818cf8 80%, #c084fc 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                filter: drop-shadow(0 10px 30px rgba(99, 102, 241, 0.25));
                transition: transform 0.3s ease, filter 0.3s ease;
                user-select: none;
            }

            .title:hover {
                transform: scale(1.02);
                filter: drop-shadow(0 15px 40px rgba(129, 140, 248, 0.4));
            }

            .actions {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .btn-docs {
                display: inline-flex;
                align-items: center;
                gap: 0.625rem;
                padding: 0.75rem 1.6rem;
                font-size: 0.9375rem;
                font-weight: 600;
                letter-spacing: 0.03em;
                color: #f1f5f9;
                text-decoration: none;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 9999px;
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .btn-docs::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(168, 85, 247, 0.25));
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .btn-docs:hover {
                color: #ffffff;
                border-color: rgba(168, 85, 247, 0.45);
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -3px rgba(99, 102, 241, 0.35), 0 0 20px rgba(168, 85, 247, 0.25);
            }

            .btn-docs:hover::before {
                opacity: 1;
            }

            .btn-docs:active {
                transform: translateY(0);
            }

            .btn-docs .icon-doc {
                width: 1.15rem;
                height: 1.15rem;
                color: #a5b4fc;
                position: relative;
                z-index: 1;
                transition: transform 0.3s ease;
            }

            .btn-docs .icon-arrow {
                width: 0.95rem;
                height: 0.95rem;
                color: #cbd5e1;
                position: relative;
                z-index: 1;
                transition: transform 0.3s ease, color 0.3s ease;
            }

            .btn-docs:hover .icon-arrow {
                transform: translateX(3px);
                color: #ffffff;
            }

            .btn-docs span {
                position: relative;
                z-index: 1;
            }

            @keyframes pulseGlow {
                0% {
                    transform: scale(0.9) translate(-20px, -20px);
                    opacity: 0.7;
                }
                100% {
                    transform: scale(1.1) translate(20px, 20px);
                    opacity: 1;
                }
            }
        </style>
    </head>
    <body>
        <div class="glow-bg"></div>
        <div class="grid-overlay"></div>

        <main class="container">
            <h1 class="title">TASKFLOW</h1>
            <div class="actions">
                <a href="{{ route('docs') }}" class="btn-docs">
                    <svg class="icon-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                        <path d="M6 6h10"></path>
                        <path d="M6 10h10"></path>
                    </svg>
                    <span>API Docs</span>
                    <svg class="icon-arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 8h10M9 4l4 4-4 4"/>
                    </svg>
                </a>
            </div>
        </main>
    </body>
</html>
