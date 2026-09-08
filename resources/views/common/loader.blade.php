<style>
    /* Loader css start */
    #loader-div {
        position: fixed;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        font-family: arial;
        z-index: 1000000;
        overflow: hidden;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    #bars1 {
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        height: 50px;
        width: 50px;
        transform: translate(-50%, -50%);
        margin: 0;
    }

    #bars1 span {
        position: absolute;
        display: block;
        bottom: 10px;
        width: 10px;
        height: 10px;
        background: rgba(0, 0, 0, 0.25);
        -webkit-animation: anim 1.5s infinite ease-in-out;
        animation: anim 1.5s infinite ease-in-out;
    }

    #bars1 span:nth-child(2) {
        left: 15px;
        -webkit-animation-delay: 0.2s;
        animation-delay: 0.2s;
    }

    #bars1 span:nth-child(3) {
        left: 30px;
        -webkit-animation-delay: 0.4s;
        animation-delay: 0.4s;
    }

    #bars1 span:nth-child(4) {
        left: 45px;
        -webkit-animation-delay: 0.6s;
        animation-delay: 0.6s;
    }

    #bars1 span:nth-child(5) {
        left: 60px;
        -webkit-animation-delay: 0.8s;
        animation-delay: 0.8s;
    }

    @keyframes anim {
        0% {
            height: 10px;
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            background: rgba(0, 0, 0, 0.25);
        }

        25% {
            height: 30px;
            -webkit-transform: translateY(10px);
            transform: translateY(10px);
            -webkit-transform: translateY(10px);
            transform: translateY(10px);
            background: #000000;
        }

        50% {
            height: 10px;
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            background: rgba(0, 0, 0, 0.25);
        }

        100% {
            height: 10px;
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            background: rgba(0, 0, 0, 0.25);
        }
    }

    @-webkit-keyframes anim {
        0% {
            height: 10px;
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            background: rgba(0, 0, 0, 0.25);
        }

        25% {
            height: 30px;
            -webkit-transform: translateY(10px);
            transform: translateY(10px);
            background: #000000;
        }

        50% {
            height: 10px;
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            background: rgba(0, 0, 0, 0.25);
        }

        100% {
            height: 10px;
            -webkit-transform: translateY(0px);
            transform: translateY(0px);
            background: rgba(0, 0, 0, 0.25);
        }
    }
</style>
<div id="loader-div" style="display: none">
    <div id="bars1">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>