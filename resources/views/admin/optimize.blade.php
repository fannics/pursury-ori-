@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-2">
                            <h3>Comandos</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="text" name="command" id="command" placeholder="Comando" />
                            <input type="submit" value="Enviar" id="send-command-button">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-xs-12">
                            <div id="command-output">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('javascripts')
    <script type="text/javascript">
        $(function(){
            $(document).on('click', '#send-command-button', function(e){
                e.preventDefault();

                $.post('{{ route('post_optimize') }}', {command: $('#command').val()})
                        .success(function(res){
                            $('#command-output').text(JSON.stringify(res));
                        })
                        .fail(function(){

                        });
            });
        });
    </script>
@endsection
