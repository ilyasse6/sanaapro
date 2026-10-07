// Créez un fichier liver-loader.blade.php dans resources/views/components/
<div class="liver-loader-container">
    <div class="liver-loader"></div>
</div>

<style>
    .liver-loader-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }

    .liver-loader {
        width: 100px;
        height: 160px;
        background-color: #8B0000;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        position: relative;
        animation: pulse 1.5s infinite alternate, rotate 3s infinite linear;
        box-shadow: 0 0 20px rgba(139, 0, 0, 0.6);
    }

    .liver-loader:before {
        content: '';
        position: absolute;
        width: 45%;
        height: 70%;
        background-color: #A52A2A;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        top: 15%;
        left: -20%;
        animation: pulse 1.5s infinite alternate-reverse;
    }

    .liver-loader:after {
        content: '';
        position: absolute;
        width: 30%;
        height: 40%;
        background-color: #800000;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        bottom: 10%;
        right: -10%;
        animation: pulse 1.5s infinite alternate;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
        }
        100% {
            transform: scale(1.05);
        }
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
