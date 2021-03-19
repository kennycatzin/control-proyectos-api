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
        .row{
            font-size: 14px;
            color: #6d6868;
            padding: 0px 15px;
            margin-top: 0px;
        }
        .columna{
            display: inline-block;
            margin-top: 0px;
        }
        .respuesta{
            display: inline-block;
            margin-top: 0px;
            font-family: source_sans_pro_regular;
            font-size: 14px;
            border: 0px;
            border-bottom: 1px #6d6868 solid;
            width: auto;
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
                <h2 >Sitio de certificaciones BUPSY</h2>
            </div>
        </div>
        <form id="enviar_post_correo">
            <hr class="divisor">
            <h4 class="fuente">Estimado: {{$empresa}}</h4>
            <p class="fuente">Muchas gracias por registrarse en el Certificado de Buenas Prácticas Sanitarias Yucatán (BUPSY), su solicitud ha sido registrada. En breve un ejecutivo de la consultora que usted seleccionó, se pondrá en contacto con usted para continuar con el proceso de certificación. </p>
            <br>
            <p class="fuente">En caso de no recibir respuesta a su solicitud en un plazo mayor a 3 días hábiles favor de enviar un correo a certificaturismo@yucatan.gob.mx</p>
            <br>
            <p class="fuente">Sin otro particular reciba un cordial saludo.</p>
        </form>

        <br>
        <hr class="divisor">
        <div>
             <p class="fuente">Este correo es informativo, no es necesario responder. <br>
                Gobierno del Estado de Yucatán<br>
        </div>
    </div>
    {{-- <div class="body">
        <div>

        </div>
        <img src="http://apidgti.yucatan.gob.mx/logos/index.php/horizontal/default" class="image">
        <h4 class="fuente">Estimado (a) {{$nombre}} :</h4>
        <p>Este mensaje ha sido enviado por un funcionario de la dependencia : </p>
        <p class="fuente">{{$nota}} </p>
    </div> --}}
    
</body>
</html>