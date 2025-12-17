<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful | BSIT Tracer</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            color: #fff;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        canvas {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .content-overlay {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            background: radial-gradient(circle at center, rgba(58, 130, 246, 0.05) 0%, transparent 70%);
        }

        .success-card {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(58, 130, 246, 0.3);
            padding: 3rem;
            border-radius: 30px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5), 0 0 20px rgba(58, 130, 246, 0.1);
            animation: cardEntrance 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.9) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .glow-text {
            text-shadow: 0 0 20px rgba(58, 130, 246, 0.5);
        }

        .btn-home {
            background: #fff;
            color: #000;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.8rem;
            letter-spacing: 0.2em;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-home:hover {
            transform: scale(1.05);
            background: #3a82f6;
            color: #fff;
            box-shadow: 0 0 20px rgba(58, 130, 246, 0.4);
        }
    </style>
</head>
<body>

    <canvas id="starCanvas"></canvas>

    <div class="content-overlay">
        <div class="success-card max-w-lg w-full mx-6">
            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-[0_0_30px_rgba(58,130,246,0.6)] animate-pulse">
                <i class="fas fa-check text-3xl"></i>
            </div>
            
            <h2 class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4 uppercase">System Sync Complete</h2>
            <h1 class="text-4xl font-black mb-6 glow-text tracking-tight">DATA RECORDED.</h1>
            
            <p class="text-gray-400 mb-10 leading-relaxed text-sm">
                Thank you for participating in the <span class="text-white font-bold">BSIT Graduate Tracer System</span>. Your professional data has been encrypted and successfully synchronized with the institutional database.
            </p>

            <div class="space-y-4">
                <a href="welcome_page.php" class="btn-home inline-block w-full">Go to Dashboard</a>
                <p class="text-[10px] text-gray-600 font-mono mt-6 uppercase tracking-widest">Tracking_Session: #<?php echo bin2hex(random_bytes(4)); ?> // Validated</p>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('starCanvas');
        const ctx = canvas.getContext('2d');

        let canvasWidth, canvasHeight;
        let stars = [];
        let shootingStars = [];

        function resize() {
            canvasWidth = window.innerWidth;
            canvasHeight = window.innerHeight;
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
        }

        window.addEventListener('resize', resize);
        resize();

        class Star {
            constructor() {
                this.x = Math.random() * canvasWidth;
                this.y = Math.random() * canvasHeight;
                this.size = Math.random() * 1.5;
                this.opacity = Math.random();
                this.speed = 0.05 + Math.random() * 0.1;
            }

            draw() {
                ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }

            update() {
                this.opacity += this.speed;
                if (this.opacity > 1 || this.opacity < 0) this.speed *= -1;
            }
        }

        class ShootingStar {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvasWidth;
                this.y = 0;
                this.len = Math.random() * 80 + 10;
                this.speed = Math.random() * 10 + 5;
                this.size = Math.random() * 1 + 0.5;
                this.waitTime = new Date().getTime() + Math.random() * 3000;
                this.active = false;
            }

            draw() {
                if (!this.active) return;
                ctx.strokeStyle = 'rgba(58, 130, 246, 0.5)';
                ctx.lineWidth = this.size;
                ctx.beginPath();
                ctx.moveTo(this.x, this.y);
                ctx.lineTo(this.x + this.len, this.y + this.len);
                ctx.stroke();
            }

            update() {
                if (!this.active && new Date().getTime() > this.waitTime) {
                    this.active = true;
                }

                if (this.active) {
                    this.x += this.speed;
                    this.y += this.speed;
                    if (this.x > canvasWidth || this.y > canvasHeight) {
                        this.reset();
                    }
                }
            }
        }

        // Initialize stars
        for (let i = 0; i < 200; i++) stars.push(new Star());
        for (let i = 0; i < 4; i++) shootingStars.push(new ShootingStar());

        function animate() {
            ctx.clearRect(0, 0, canvasWidth, canvasHeight);
            
            stars.forEach(star => {
                star.update();
                star.draw();
            });

            shootingStars.forEach(sStar => {
                sStar.update();
                sStar.draw();
            });

            requestAnimationFrame(animate);
        }

        animate();
    </script>
</body>
</html>