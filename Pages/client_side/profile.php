<?php
require_once('../db.php');
session_start();

// Verificar si el usuario está logueado
if(!isset($_SESSION['id_usuario'])) {
    header("location: ../Login.php");
    exit();
}

// Obtener datos del usuario que inició sesión
$id_usuario = $_SESSION['id_usuario'];
$id_cargo = $_SESSION['id_cargo'];

// Consulta para obtener información del cargo
$query1 = "SELECT * FROM cargo WHERE id = '$id_cargo'";
$result1 = mysqli_query($conexion, $query1);

// Consulta para obtener información del usuario
$query2 = "SELECT * FROM usuarios WHERE ID = '$id_usuario'";
$result2 = mysqli_query($conexion, $query2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <!--<link rel="stylesheet" href="ClientDesign.css">-->
  <script type="text/javascript" src="app.js" defer></script>
  <script type="text/javascript" src="openPopup.js" defer></script>
</head>
<body>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
      :root{
        --base-clr: #11121a;
        --line-clr: #42434a;
        --hover-clr: #D1323936;
        --text-clr: #e6e6ef;
        --accent-clr: #D13239;
        --secondary-text-clr: #b0b3c1;
        --sidebar: #ffffff;
      }
      *{
        margin: 0;
        padding: 0;
      }
      html{
        font-family: Poppins, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.5rem;
      }
      body{
        min-height: 100vh;
        min-height: 100dvh;
        background-color: var(--sidebar);
        color: var(--text-clr);
        display: grid;
        grid-template-columns: auto 1fr;
      }
      #sidebar{
        box-sizing: border-box;
        height: 100vh;
        width: 250px;
        padding: 5px 1em;
        background-color: var(--sidebar);
        box-shadow: 5px 0px 10px rgba(0, 0, 0, 0.276);
        position: sticky;
        top: 0;
        align-self: start;
        transition: 300ms ease-in-out;
        overflow: hidden;
        text-wrap: nowrap;
      }
      #sidebar.close{
        padding: 5px;
        width: 59px;
      }
      #sidebar.close a, #sidebar.close .dropdown-btn {
        white-space: nowrap;
      }

      ul{
        list-style: none;
      }
      #sidebar > ul > li:first-child{
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;
        .logo{
          font-weight: 600;
        }
      }
      #sidebar ul li.active a{
        color: var(--base-clr);

        svg{
          fill: var(--accent-clr);
        }
      }

      #sidebar a, #sidebar .dropdown-btn, #sidebar .logo{
        border-radius: .5em;
        padding: .85em;
        text-decoration: none;
        color: var(--base-clr);
        display: flex;
        align-items: center;
        gap: 1em;
        white-space: normal; 
        word-wrap: break-word;
      }
      .dropdown-btn{
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        font: inherit;
        cursor: pointer;
      }
      #sidebar svg{
        flex-shrink: 0;
        fill: var(--accent-clr);
      }
      #sidebar a span, #sidebar .dropdown-btn span{
        flex-grow: 1;
      }
      #sidebar a:hover, #sidebar .dropdown-btn:hover{
        background-color: var(--hover-clr);
      }
      #sidebar .sub-menu{
        display: grid;
        grid-template-rows: 0fr;
        transition: 300ms ease-in-out;

        > div{
          overflow: hidden;
        }
      }
      #sidebar .sub-menu.show{
        grid-template-rows: 1fr;
      }
      .dropdown-btn svg{
        transition: 200ms ease;
      }
      .rotate svg:last-child{
        rotate: 90deg;
      }
      #sidebar .sub-menu a{
        padding-left: 2em;
      }
      #toggle-btn{
        margin-left: auto;
        padding: 1em;
        border: none;
        border-radius: .5em;
        background: none;
        cursor: pointer;

        svg{
          transition: rotate 150ms ease;
        }
      }
      #toggle-btn:hover{
        background-color: var(--hover-clr);
      }

      main {
        background-color: #F6F7FB;
      }

      main p{
        color: var(--secondary-text-clr);
        margin-top: 5px;
        margin-bottom: 15px;
      }
      .container{
        border: 1px solid var(--line-clr);
        border-radius: 1em;
        margin: 20px;
        padding: min(3em, 15%);

        h2, p { margin-top: 1em }
      }

      @media(max-width: 800px){
        body{
          grid-template-columns: 1fr;
        }
        
        .container{
          border: none;
          padding: 0;
        }
        #sidebar{
          height: 60px;
          width: 100%;
          border-right: none;
          border-top: 1px solid var(--line-clr);
          padding: 0;
          position: fixed;
          top: unset;
          bottom: 0;

          > ul{
            padding: 0;
            display: grid;
            grid-auto-columns: 60px;
            grid-auto-flow: column;
            align-items: center;
            overflow-x: scroll;
          }
          ul li{
            height: 100%;
          }
          ul a, ul .dropdown-btn{
            width: 60px;
            height: 60px;
            padding: 0;
            border-radius: 0;
            justify-content: center;
          }

          ul li span, ul li:first-child, .dropdown-btn svg:last-child{
            display: none;
          }

          ul li .sub-menu.show{
            position: fixed;
            bottom: 60px;
            left: 0;
            box-sizing: border-box;
            height: 60px;
            width: 100%;
            background-color: var(--hover-clr);
            border-top: 1px solid var(--line-clr);
            display: flex;
            justify-content: center;

            > div{
              overflow-x: auto;
            }
            li{
              display: inline-flex;
            }
            a{
              box-sizing: border-box;
              padding: 1em;
              width: auto;
              justify-content: center;
            }
          }
        }
      }

      .navbar-white {
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }

      .navbar-white .menu-icon {
        display: flex;
        align-items: center;
      }

      .navbar-white .menu-icon div {
        width: 20px;
        height: 2px;
        background-color: #888;
        margin: 3px 0;
      }

      .navbar-white .profile {
        display: flex;
        align-items: center;
        position: relative;
        padding: 5px 11px;
        border-radius: 5px;
        cursor: pointer;
      }


      .navbar-white .separator {
        width: 2px;
        height: 40px;
        background-color: #ddd;
        margin: 0 10px;
        border-radius: 1px 1px 1px 1px;
      }

      .navbar-white .profile-content {
        display: flex;
        align-items: center;
        padding: 3px 10px 3px 10px;
        border-radius: 5px;
      }

      .navbar-white .profile-content:hover {
        background-color: #11121a15;
      }
      

      .navbar-white .notification {
        position: relative;
      }

      .navbar-white .notification img {
        width: 300px;
        height: 300px;
      }

      .navbar-white .notification .badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        color: white;
        font-size: 10px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .navbar-white .profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
      }

      .navbar-white .profile .info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar-white .profile .info span {
        color: #333;
        font-size: 14px;
        font-weight: bold;
      }

      .navbar-white .profile .info .cargo {
        color: #aaa;
        font-size: 12px;
        font-weight: normal;
      }

      .header {
        background-color: #b71c1c;
        color: white;
        padding: 15px;
        font-size: 14px;
        font-weight: bold;
      }

      .grid-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-weight: bold;
        display: block;
        padding: 50px;
      }

      .grid-container .grid-item {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
        padding: 2rem;
        display:flex;
        gap:30px;
      }

      .grid-container .grid-sidebar {
        background-color: red;
        border-radius: 15px;
      }

      .profile-content {
        opacity:0.5;
        border:none;
        background-color: white;
      }

      .profile-content {
        opacity: 0.9;
      }

      .grid-container .grid-sidebar {
        background-color: white;
      }

      .grid-container .grid-item a {
        text-decoration: none;
        color:rgb(209, 50, 58);
      }

      .grid-container .grid-item li {
        margin-bottom: 20px;
        border-radius: 20px;
        text-align:center;
        padding:10px;
        padding-left: 20px;              
        padding-right: 20px;            
        background-color:rgba(209, 50, 58, 0.28);
        cursor:pointer;
      }
    
      .popup {
        display: none;
        position: absolute;
        background-color: #fff;
        z-index: 1000;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
        padding: 10px;
        margin-top:100px;
        border-radius: 5px;
        flex-direction: column;
      }

      .popup.show {
        display: flex;
      }


    </style>
  <nav id="sidebar">
    <ul>
      <li>
        <img src="logotransparente.png" alt="Dinosaur" height="55px"/>
        <button onclick=toggleSidebar() id="toggle-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
        </button>
      </li>
      <li>
        <a href="Consagracion.php">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 256 256" xml:space="preserve" fill = "#D13239"><g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: #D13239; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" ><path d="M 75.715 21.881 h -22.44 V 2.14 c 0 -1.182 -0.958 -2.14 -2.14 -2.14 h -12.27 c -1.182 0 -2.14 0.958 -2.14 2.14 v 19.741 h -22.44 c -1.182 0 -2.14 0.958 -2.14 2.14 v 12.27 c 0 1.182 0.958 2.14 2.14 2.14 h 22.44 V 87.86 c 0 1.182 0.958 2.14 2.14 2.14 h 12.27 c 1.182 0 2.14 -0.958 2.14 -2.14 V 38.432 h 22.44 c 1.182 0 2.14 -0.958 2.14 -2.14 v -12.27 C 77.855 22.839 76.897 21.881 75.715 21.881 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: #D13239; fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" /></g></svg>          
          <span>Consagración</span>
        </a>
      </li>
      <li>
        <a href="Mision.php">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="24" height="24" fill = "#D13239"><path d="M320 0a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm44.7 164.3L375.8 253c1.6 13.2-7.7 25.1-20.8 26.8s-25.1-7.7-26.8-20.8l-4.4-35-7.6 0-4.4 35c-1.6 13.2-13.6 22.5-26.8 20.8s-22.5-13.6-20.8-26.8l11.1-88.8L255.5 181c-10.1 8.6-25.3 7.3-33.8-2.8s-7.3-25.3 2.8-33.8l27.9-23.6C271.3 104.8 295.3 96 320 96s48.7 8.8 67.6 24.7l27.9 23.6c10.1 8.6 11.4 23.7 2.8 33.8s-23.7 11.4-33.8 2.8l-19.8-16.7zM40 64c22.1 0 40 17.9 40 40l0 40 0 80 0 40.2c0 17 6.7 33.3 18.7 45.3l51.1 51.1c8.3 8.3 21.3 9.6 31 3.1c12.9-8.6 14.7-26.9 3.7-37.8l-15.2-15.2-32-32c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l32 32 15.2 15.2c0 0 0 0 0 0l25.3 25.3c21 21 32.8 49.5 32.8 79.2l0 78.9c0 26.5-21.5 48-48 48l-66.7 0c-17 0-33.3-6.7-45.3-18.7L28.1 393.4C10.1 375.4 0 351 0 325.5L0 224l0-64 0-56C0 81.9 17.9 64 40 64zm560 0c22.1 0 40 17.9 40 40l0 56 0 64 0 101.5c0 25.5-10.1 49.9-28.1 67.9L512 493.3c-12 12-28.3 18.7-45.3 18.7L400 512c-26.5 0-48-21.5-48-48l0-78.9c0-29.7 11.8-58.2 32.8-79.2l25.3-25.3c0 0 0 0 0 0l15.2-15.2 32-32c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-32 32-15.2 15.2c-11 11-9.2 29.2 3.7 37.8c9.7 6.5 22.7 5.2 31-3.1l51.1-51.1c12-12 18.7-28.3 18.7-45.3l0-40.2 0-80 0-40c0-22.1 17.9-40 40-40z"/></svg>          
          <span>Misión</span>
        </a>
      </li>
      
      <li>
        <a href="Formacion.php">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24" fill = "#D13239"><path d="M96 0C43 0 0 43 0 96L0 416c0 53 43 96 96 96l288 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64c17.7 0 32-14.3 32-32l0-320c0-17.7-14.3-32-32-32L384 0 96 0zm0 384l256 0 0 64L96 448c-17.7 0-32-14.3-32-32s14.3-32 32-32zM208 80c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 48 48 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-48 0 0 112c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-112-48 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16l48 0 0-48z"/></svg>          
          <span>Formación</span>
        </a>
      </li>
      <li>
        <a href="dinstitucional.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"  viewBox="0 0 512 512"><path d="M243.4 2.6l-224 96c-14 6-21.8 21-18.7 35.8S16.8 160 32 160l0 8c0 13.3 10.7 24 24 24l400 0c13.3 0 24-10.7 24-24l0-8c15.2 0 28.3-10.7 31.3-25.6s-4.8-29.9-18.7-35.8l-224-96c-8-3.4-17.2-3.4-25.2 0zM128 224l-64 0 0 196.3c-.6 .3-1.2 .7-1.8 1.1l-48 32c-11.7 7.8-17 22.4-12.9 35.9S17.9 512 32 512l448 0c14.1 0 26.5-9.2 30.6-22.7s-1.1-28.1-12.9-35.9l-48-32c-.6-.4-1.2-.7-1.8-1.1L448 224l-64 0 0 192-40 0 0-192-64 0 0 192-48 0 0-192-64 0 0 192-40 0 0-192zM256 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/></svg>          
          <span>Desarrollo Institucional</span>
        </a>
      </li>
      <li>
        <a href="fsalesiana.php">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="24" height="24" fill = "#D13239"><path d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5l0 38.5c0 17.7-14.3 32-32 32l-48 0c-17.7 0-32-14.3-32-32l0-38.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3l19.5 0c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3l19.5 0c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6l19.5 0c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5l0 38.5c0 17.7-14.3 32-32 32l-48 0c-17.7 0-32-14.3-32-32l0-38.5-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5l0 54.5c0 17.7-14.3 32-32 32l-48 0c-17.7 0-32-14.3-32-32l0-54.5-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"/></svg>
          <span>Familia Salesiana</span>
        </a>
      </li>
      <li>
        <a href="Dashboard.php">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="24" height="24" fill = "#D13239"><path d="M304 240l0-223.4c0-9 7-16.6 16-16.6C443.7 0 544 100.3 544 224c0 9-7.6 16-16.6 16L304 240zM32 272C32 150.7 122.1 50.3 239 34.3c9.2-1.3 17 6.1 17 15.4L256 288 412.5 444.5c6.7 6.7 6.2 17.7-1.5 23.1C371.8 495.6 323.8 512 272 512C139.5 512 32 404.6 32 272zm526.4 16c9.3 0 16.6 7.8 15.4 17c-7.7 55.9-34.6 105.6-73.9 142.3c-6 5.6-15.4 5.2-21.2-.7L320 288l238.4 0z"/></svg>          
          <span>Dashboards</span>
        </a>
      </li>
      <li class="active">
        <a href="profile.php  ">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24" fill = "#D13239"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>          
          <span>Perfil</span>
        </a>
      </li>
    </ul>
  </nav>
    <main>
      <div class="navbar-white">
        <div class="menu-icon"></div>
        <div class="profile">
          <div class="notification">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24" fill = "rgb(30, 48, 80)"><path d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"/></svg>
            <div class="badge">1</div>
          </div>
          <div class="separator"></div>

          <button onclick="openPopup()"id="showpopup" class="profile-content">
            <img src="profileVector.jpg" alt="User"> 
            <div class="info">
              <span><?php $data= mysqli_fetch_assoc($result2); echo strtoupper($data['usuarios']);?></span>
              <span class="cargo"><?php $data= mysqli_fetch_assoc($result1); echo strtoupper($data['descripcion']);?></span>
            </div>
          </button>

          <div class="popup" id="myPopup">
            <a href="#">Ver Perfil</a>
            <a href="#">Configuración</a>
            <a href="#">Cerrar Sesión</a>
          </div>
          
        </div>
      </div>
    

  <div class="header">
      PERFIL
  </div>

  <div class="grid-container">
      <div class="grid-item">
        <nav class="grid-sidebar">
          <ul>
            <li onclick="window.location.href='profile.php';">
              <a href="#">Mi perfil</a>
            </li>
            <li onclick="window.location.href='profile.php';">
              <a href="#">Seguridad</a>
            </li>
            <li onclick="window.location.href='profile.php';">
              <a href="#">Notificaciones</a>
            </li >
            <li onclick="window.location.href='profile.php';">
              <a href="#">Preferencias</a>
            </li>
          </ul>
        </nav>
        <div>
          <h1>My Profile</h1>
        </div>
      </div>
  </div>
  <!--<div class="container">
    <div class = "containerII">
      <h2>Profile</h2>
      <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Veritatis porro iure quaerat aliquam! Optio dolorum in eum provident, facilis error repellendus excepturi enim dolor deleniti adipisci consectetur doloremque, unde maiores odit sapiente. Atque ab necessitatibus laboriosam consequatur eius similique, ex dolorum eum eaque sequi id veritatis voluptates perspiciatis, cupiditate pariatur.</p>
    </div>
  </div>-->
  </main>
</body>
</html>