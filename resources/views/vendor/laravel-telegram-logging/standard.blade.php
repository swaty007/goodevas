{{ $icon }}<b>{{ $appName }}</b> {{ $appEnv }}.{{ $level_name }}
<i>[{{ $datetime->format('Y-m-d H:i:s') }}]</i>
@if(Auth::id())
User/Admin ID: {{ Auth::id() }} {{ get_class(Auth::user()) }}
@endif
@if(!empty($context['exception']))
Class: {{ get_class($context['exception']) }}
File: {{ $context['exception']->getFile() }}
Line: {{ $context['exception']->getLine() }}
Code: {{ $context['exception']->getCode() }}
@if(!empty($context['exception']->getPrevious()))
<i>Previous Exception:
Class: {{ get_class($context['exception']->getPrevious()) }}
File: {{ $context['exception']->getPrevious()->getFile() }}
Line: {{ $context['exception']->getPrevious()->getLine() }}
Code: {{ $context['exception']->getPrevious()->getCode() }}</i>
@endif
@endif
<blockquote expandable><pre><code class="language-php">{{ $formatted }}</code></pre></blockquote>
