
<h2 style="h2 {color: #2d315f;}">Listado de pendientes</h2>
<h4>Fecha y hora del reporte:  {{ date('d-m-Y H:i:s') }}</h4>
<br>
<table>
    <thead>
    <tr>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">#</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Titulo</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Descripcion</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Prioridad</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Solicitante</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Fecha</th>
    </tr>
    </thead>
    <tbody>
    @foreach($Pendientes as $empresa)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $empresa->titulo }}</td>
            <td>{{ $empresa->descripcion }}</td>
            <td>{{ $empresa->prioridad }}</td>
            <td>{{ $empresa->solicitante }}</td>           
            <td>{{ $empresa->fecha = date("d-m-Y", strtotime($empresa->fecha_creacion)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>