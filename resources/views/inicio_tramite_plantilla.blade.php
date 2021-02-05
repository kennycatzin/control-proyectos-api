<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Plataforma de Gestión</title>
    <style type="text/css">
        .body{
            width: 60%;
            text-align: center;
            margin: auto;
        }
        .fuente{
            text-align: start;
            color: #6E799F;
            font-size: 16px; 
        }
        .btn{
            padding: 10px 60px;
            background-color: #00b6f0;
            color: white;
            font-size: 16px;
            margin: auto;
            display: inline-block;
            cursor: pointer;
            text-decoration: none;
        }
        A:link {text-decoration: none }


        .space{
            margin-bottom: 10px;
        }
        .image{
            width: 200px;
        }
        .divisor{
            background-color: #a1a1a1;
            height: 4px;

        }
  
        .flexContainer { 
   
            margin: 2px 10px;
            display: flex;
        } 
 
 .left {
   flex-basis : 30%;
 }
 
 .right {
   flex-basis : 30%;
   margin-left: 170px;
 }

        @media screen and (max-width: 600px){
            .body{
            width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="body">
        
        <div class="flexContainer">
            <div class="left">  
            <img src="http://apidgti.yucatan.gob.mx/logos/index.php/horizontal/default" class="image">
            {{-- <img src="'{{  $rutaImagen }}'" class="image"> --}}
            </div>
            <div class="right">
                <h2 >Ventanilla Digital de Inversiones</h2>
            </div>
        </div>
        <form action="{{$href}}" method="POST" id="enviar_post_correo">
            <input type="hidden"  name="id_solicitud" value="{{$detalle}}">
            <input type="hidden"  name="tipo" value="{{$tipo}}">

          
            <hr class="divisor">
            <h4 class="fuente">Estimado (a) {{$nombre}},</h4>
        <p class="fuente">Tu solicitud {{$nombre_tramite}} con folio {{$folio}} se registró exitosamente.</p>
            <p class="fuente space">Puedes realizar o continuar la gestión de tu trámite haciendo clic en el siguiente botón.</p>
            <a href="#" class="ico_seguimiento" ></a>

            <button  class="btn" type="submit">Ver trámite</button>
            <p class="fuente">Una vez concluida tu solicitud, analizaremos tu información y en caso de requerir datos adicionales nos comunicaremos contigo a través de este medio.</p>
            <p class="fuente">¡Gracias por tu confianza!</p>
        </form>

        <br>
        <hr class="divisor">
        <div>
             <p class="fuente">Este correo es informativo, no es necesario responder. <br>
             Gobierno del estado Yucatán<br>
             <a href="https://tuempresa.yucatan.gob.mx/">tuempresa.yucatan.gob.mx</a> </p>
        </div>
       

    </div>
    
</body>
</html>