<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ventanilla Digital de Inversiones</title>
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

 .alerta{
     color:#FF8400 ;
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
        <h1>{{$ambiente}}</h1>
        <div class="fuente">
            {{$html}}
        </div>
        <hr class="divisor">
        <div>
             <p class="fuente">Este correo es informativo, no es necesario responder.<br>
             Gobierno del estado Yucatán<br>
             <a href="https://tuempresa.yucatan.gob.mx/">tuempresa.yucatan.gob.mx</a> </p>
        </div>
       

    </div>
    
</body>
</html>