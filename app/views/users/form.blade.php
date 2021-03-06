@extends("master.layout")

@section("title")
    {{ $title }}
@stop

@section("styles")
    @parent
    <link type="text/css" href="/css/jquery-datepick.css" rel="stylesheet" />
@stop

@section("page_messages")
    @include('pvadmin.partials.flash')
@stop

@section("content")
    <header class="page-header">
        <h1>{{ $title }}</h1>
    </header>
    <h2 class="sr-only">User Form Fields</h2>
    {{ Form::horizontal(['route' => $action, 'class' => 'row', 'method' => $method]) }}
        <div class="col-sm-10">
            @if (null === $user->id)
                {{-- Test if we're creating a new user --}}
                {{ ControlGroup::generate(
                    Form::label('email', 'Email'),
                    Form::email('email', Input::old('email') ?: $user->email, ['required']) . $errors->first('email', '<span class="label label-danger">:message</span>'),
                    null,
                    3
                ) }}
                {{ Form::hidden('auto_password', 1) }}
            @else
                {{ ControlGroup::generate(
                    Form::label('email', 'Email'),
                    Form::email('email', Input::old('email') ?: $user->email, ['disabled']),
                    null,
                    3
                ) }}
                {{ ControlGroup::generate(
                    Form::label('password', 'New Password'),
                    Form::text('password') . $errors->first('password', '<span class="label label-danger">:message</span>'),
                    null,
                    3
                ) }}
                {{ ControlGroup::generate(
                    Form::label('password_confirmation', 'Confirm New Password'),
                    Form::password('password_confirmation') . $errors->first('password_confirmation', '<span class="label label-danger">:message</span>'),
                    null,
                    3
                ) }}
                <div class='form-group'>
                    <div class="control-label col-sm-3 label-generate-password">Generate Password</div>
                    <div class='col-sm-9'>
                        <button type='button' class='btn btn-success' id='generate_password'>Generate a New Password</button>
                        <button type='button' class='btn btn-primary' id='clear_password'>Clear Password Field</button>
                    </div>
                </div>
            @endif
            {{ ControlGroup::generate(
                Form::label('company', 'Company'),
                Form::text('company', Input::old('company') ?: $user->company, ['required']) . $errors->first('company', '<span class="label label-danger">:message</span>'),
                null,
                3
            ) }}
            {{ ControlGroup::generate(
                Form::label('expires', 'Expiration Date'),
                Form::text('expires', Input::old('expires') ?: $user->expires, ['required']) . $errors->first('expires', '<span class="label label-danger">:message</span>'),
                null,
                3
            ) }}
            {{ ControlGroup::generate(
                Form::label('isr_contact_id', 'ISR'),
                Form::select('isr_contact_id', $isrs, $user->isr_contact_id ?: Auth::id(), ['required']) . $errors->first('isr_contact_id', '<span class="label label-danger">:message</span>'),
                null,
                3
            ) }}
            <div class='form-group'>
            <label class="col-sm-3 control-label">
                Demo Access
            </label>
            <div class="col-sm-9">
                <?php 
                    $previous_language =  'none';
                    $previous_enterprise_version = 'none'; 
                ?>
                @forelse($demos as $demo)
                    @if (($demo->language != $previous_language) || ($demo->enterprise_version != $previous_enterprise_version))
                        {{ '<h3>'.$demo->language.' - PVE '.$demo->enterprise_version.'</h3>' }}
                    @endif
                    @if (null === $user->id)
                        <?php $checked = false; ?>
                    @else
                        <?php 
                            if (is_array($user_demo_access)) {
                                in_array($demo->id, $user_demo_access) ? $checked = true : $checked = false;
                            } else {
                                $checked = false;
                            }
                        ?>
                    @endif
                    {{ Form::checkbox('demo-access[]', $demo->id, $checked, array('class' => 'demo-access', 'id' => 'demo-'.$demo->id)) }} {{ Form::label('demo-'.$demo->id, $demo->title) }}<br />
                    <?php 
                        $previous_language =  $demo->language;
                        $previous_enterprise_version = $demo->enterprise_version; 
                    ?>
                @empty
                    <p>What, no demos?</p>
                @endforelse
            </div>
            </div>
            <div class='form-group'>
                <div class='col-sm-3'>&nbsp;</div>
                <div class='col-sm-4'>
                    <div class="well">
                        {{ Button::primary('Submit')->submit()->block() }}
                    </div>
                </div>
                <div class='col-sm-5'>&nbsp;</div>
            </div>
        </div>
    {{ Form::close() }}
@stop

@section("scripts")
    @parent
    <script src="/js/jquery-datepick.js"></script>
    <script>
        $(function() {
            $('#expires').datepick();
        });
        $(document).ready(function(){ 
            $("#generate_password").click(function(){
                var the_password = generatePassword();
                $("#password").val(the_password);
                $("#password_confirmation").val(the_password);
                $(this).blur();
            });
            $("#clear_password").click(function(){
                $("#password").val('');
                $("#password_confirmation").val('');
                $(this).blur();
            });
        });
        function generatePassword() {
            var length = 8,
                charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }
    </script>
@stop
