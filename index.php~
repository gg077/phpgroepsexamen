<?php require_once("includes/header.php"); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gekke Design</title>
    <style>
        body {
            background: linear-gradient(45deg, #ff0099, #493240);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            color: white;
            overflow: hidden;
        }
        .crazy-button {
            font-size: 2rem;
            padding: 15px 30px;
            background: #ffcc00;
            border: 3px dashed #000;
            border-radius: 15px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease-in-out;
            animation: shake 0.5s infinite alternate;
            z-index: 10;
        }
        .crazy-button:hover {
            transform: rotate(10deg) scale(1.1);
        }
        @keyframes shake {
            from { transform: rotate(-5deg); }
            to { transform: rotate(5deg); }
        }
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
    </style>
</head>
<body>
<canvas id="particleCanvas"></canvas>
<a class="crazy-button" href="login.php">🚀 Ga naar Login 🚀</a>
<script>
    const canvas = document.getElementById("particleCanvas");
    const ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    let particlesArray = [];
    const numberOfParticles = 100;

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 5 + 1;
            this.speedX = (Math.random() * 1.5 - 0.75) * 0.3;
            this.speedY = (Math.random() * 1.5 - 0.75) * 0.3;
            this.life = 15 * 60; // 15 seconden op 60FPS
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.size > 0.2) this.size -= 0.02;
            this.life--;
        }
        draw() {
            ctx.fillStyle = 'rgba(255,255,255,0.8)';
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function init() {
        for (let i = 0; i < numberOfParticles; i++) {
            particlesArray.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let i = particlesArray.length - 1; i >= 0; i--) {
            particlesArray[i].update();
            particlesArray[i].draw();
            if (particlesArray[i].life <= 0) {
                particlesArray.splice(i, 1);
            }
        }
        if (particlesArray.length < numberOfParticles) {
            particlesArray.push(new Particle());
        }
        requestAnimationFrame(animate);
    }

    init();
    animate();
</script>
</body>
</html>
