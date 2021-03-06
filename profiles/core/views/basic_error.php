<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

$error_id = uniqid('error');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title><?php echo $type ?></title>
    <style type="text/css">
      #aw_error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
      #aw_error h1,
      #aw_error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #911; color: #fff; }
      #aw_error h1 a,
      #aw_error h2 a { color: #fff; }
      #aw_error h2 { background: #222; }
      #aw_error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
      #aw_error p { margin: 0; padding: 0.2em 0; }
      #aw_error a { color: #1b323b; }
      #aw_error pre { overflow: auto; white-space: pre-wrap; }
      #aw_error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
      #aw_error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
      #aw_error div.content { padding: 0.4em 1em 1em; overflow: hidden; }
      #aw_error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
      #aw_error pre.source span.line { display: block; }
      #aw_error pre.source span.highlight { background: #f0eb96; }
      #aw_error pre.source span.line span.number { color: #666; }
      #aw_error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
      #aw_error ol.trace li { margin: 0; padding: 0; }
      .js .collapsed { display: none; }
    </style>
    <script type="text/javascript">
    document.documentElement.className = document.documentElement.className + ' js';
    function toggle(elem) {
    	elem = document.getElementById(elem);

    	if (elem.style && elem.style['display']) {
    		// Only works with the "style" attr
    		var disp = elem.style['display'];
      }
    	else if (elem.currentStyle) {
    		// For MSIE, naturally
    		var disp = elem.currentStyle['display'];
      }
    	else if (window.getComputedStyle) {
    		// For most other browsers
    		var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');
      }

    	// Toggle the state of the "display" style
    	elem.style.display = disp == 'block' ? 'none' : 'block';
    	return false;
    }
    </script>
  </head>
  <body>
    <div id="aw_error">
    	<h1><span class="type"><?php echo $type ?> [ <?php echo $code; ?> ]:</span> <span class="message"><?php echo nl2br($message); ?></span></h1>
    	<div id="<?php echo $error_id; ?>" class="content">
    		<p><span class="file"><?php echo $file; ?> [ <?php echo $line; ?> ]</span></p>
    		<?php echo Backtrace::source($file, $line); ?>
    		<ol class="trace">
    		<?php foreach ($backtrace as $i => $step): ?>
    			<li>
    				<p>
    					<span class="file">
    						<?php if ($step['file']): $source_id = $error_id . 'source' . $i; ?>
    							<a href="#<?php echo $source_id; ?>" onclick="return toggle('<?php echo $source_id; ?>')"><?php echo $step['file']; ?> [ <?php echo $step['line']; ?> ]</a>
    						<?php else: ?>
    							{<?php echo Literal::getPhrase('PHP internal call', 'system'); ?>}
    						<?php endif ?>
    					</span>
    					&raquo;
    					<?php echo $step['function']; ?>(<?php if ($step['args']): $args_id = $error_id.'args'.$i; ?><a href="#<?php echo $args_id; ?>" onclick="return toggle('<?php echo $args_id; ?>')"><?php echo 'arguments'; ?></a><?php endif; ?>)
    				</p>
    				<?php if (isset($args_id)): ?>
    				<div id="<?php echo $args_id; ?>" class="collapsed">
    					<table cellspacing="0">
    					<?php foreach ($step['args'] as $name => $arg): ?>
    						<tr>
    							<td><code><?php echo $name; ?></code></td>
    							<td width="100%"><pre><?php echo Debug::dump($arg); ?></pre></td>
    						</tr>
    					<?php endforeach ?>
    					</table>
    				</div>
    				<?php endif ?>
    				<?php if (isset($source_id)): ?>
    					<pre id="<?php echo $source_id ?>" class="source collapsed"><code><?php echo $step['source'] ?></code></pre>
    				<?php endif ?>
    			</li>
    			<?php unset($args_id, $source_id); ?>
    		<?php endforeach ?>
    		</ol>
    	</div>
    	<h2><a href="#<?php echo $env_id = $error_id.'environment' ?>" onclick="return toggle('<?php echo $env_id ?>')"><?php echo Literal::getPhrase('Environment', 'system'); ?></a></h2>
    	<div id="<?php echo $env_id ?>" class="content collapsed">
    		<?php $debugLog = array_reverse(Debug::getLog(), TRUE) ?>
    		<h3><a href="#<?php echo $env_id = $error_id.'debug_info' ?>" onclick="return toggle('<?php echo $env_id ?>')"><?php echo Literal::getPhrase('Debug Information', 'system'); ?></a> (<?php echo count($debugLog) ?>)</h3>
    		<div id="<?php echo $env_id ?>" class="collapsed">
    			<table cellspacing="0">
    				<?php foreach ($debugLog as $key => $value): ?>
    				<tr>
    					<td><code><?php echo $key ?></code></td>
    					<td><pre><?php echo Debug::dump($value) ?></pre></td>
    				</tr>
    				<?php endforeach ?>
    			</table>
    		</div>
    		<?php $included = get_included_files() ?>
    		<h3><a href="#<?php echo $env_id = $error_id.'environment_included' ?>" onclick="return toggle('<?php echo $env_id ?>')"><?php echo Literal::getPhrase('Included Files', 'system'); ?></a> (<?php echo count($included) ?>)</h3>
    		<div id="<?php echo $env_id ?>" class="collapsed">
    			<table cellspacing="0">
    				<?php foreach ($included as $file): ?>
    				<tr>
    					<td><code><?php echo $file ?></code></td>
    				</tr>
    				<?php endforeach ?>
    			</table>
    		</div>
    		<?php $included = get_loaded_extensions() ?>
    		<h3><a href="#<?php echo $env_id = $error_id.'environment_loaded' ?>" onclick="return toggle('<?php echo $env_id ?>')"><?php echo Literal::getPhrase('Loaded Extensions', 'system'); ?></a> (<?php echo count($included) ?>)</h3>
    		<div id="<?php echo $env_id ?>" class="collapsed">
    			<table cellspacing="0">
    				<?php foreach ($included as $file): ?>
    				<tr>
    					<td><code><?php echo $file ?></code></td>
    				</tr>
    				<?php endforeach ?>
    			</table>
    		</div>
    		<?php foreach (array('_SESSION', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER') as $var): ?>
    		<?php if (empty($GLOBALS[$var]) || ! is_array($GLOBALS[$var])) continue ?>
    		<h3><a href="#<?php echo $env_id = $error_id.'environment'.strtolower($var) ?>" onclick="return toggle('<?php echo $env_id ?>')">$<?php echo $var ?></a></h3>
    		<div id="<?php echo $env_id ?>" class="collapsed">
    			<table cellspacing="0">
    				<?php foreach ($GLOBALS[$var] as $key => $value): ?>
    				<tr>
    					<td><code><?php echo $key ?></code></td>
    					<td><pre><?php echo Debug::dump($value) ?></pre></td>
    				</tr>
    				<?php endforeach ?>
    			</table>
    		</div>
    		<?php endforeach ?>
    	</div>
      <?php if (function_exists('xdebug_time_index')): ?>
        <p>Time taken to load page: <?php echo xdebug_time_index(); ?>
      <?php endif; ?>
    </div>
  </body>
</html>