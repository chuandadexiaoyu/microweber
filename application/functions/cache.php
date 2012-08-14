<?
function cache_file_memory_storage($path) {
	static $mem = array ();
	$path_md = crc32 ( $path );
	if ($mem ["{$path_md}"] != false) {
		return $mem [$path_md];
	}
	$cont = @ file_get_contents ( $path );
	$mem [$path_md] = $cont;
	return $cont;
}
function cache_get_file_path($cache_id, $cache_group = 'global') {
	$cache_group = str_replace ( '/', DIRECTORY_SEPARATOR, $cache_group );
	$f = cache_get_dir ( $cache_group ) . DIRECTORY_SEPARATOR . $cache_id . CACHE_FILES_EXTENSION;
	
	return $f;
}

/**
 *
 *
 * Deletes cache directory for given $cache_group recursively.
 *
 * @param string $cache_group
 *        	(default is 'global') - this is the subfolder in the cache dir.
 * @return boolean
 * @author Peter Ivanov
 * @since Version 1.0
 */
function cache_clean_group($cache_group = 'global') {
	try {
		$dir = cache_get_dir ( 'global' );
		
		if (is_dir ( $dir )) {
			recursive_remove_directory ( $dir );
		}
		$dir = cache_get_dir ( $cache_group );
		
		if (is_dir ( $dir )) {
			recursive_remove_directory ( $dir );
		}
		return true;
	} catch ( Exception $e ) {
		return false;
		// $cache = false;
	}
}

/**
 *
 *
 * Gets the full path cache directory for cache group
 * Also seperates the group in subfolders for each 1000 cache files
 * for performance reasons on huge sites.
 *
 * @param string $cache_group
 *        	(default is 'global') - this is the subfolder in the cache dir.
 * @return string
 * @author Peter Ivanov
 * @since Version 1.0
 */
function cache_get_dir($cache_group = 'global', $deleted_cache_dir = false) {
	$function_cache_id = false;
	$args = func_get_args ();
	foreach ( $args as $k => $v ) {
		$function_cache_id = $function_cache_id . serialize ( $k ) . serialize ( $v );
	}
	$function_cache_id = __FUNCTION__ . crc32 ( $function_cache_id );
	$cache_content = 'CACHE_GET_DIR_' . $function_cache_id;
	
	if (! defined ( $cache_content )) {
		// define($cache_content, '1');
	} else {
		// var_dump(constant($cache_content));
		return (constant ( $cache_content ));
	}
	
	if (strval ( $cache_group ) != '') {
		$cache_group = str_replace ( '/', DIRECTORY_SEPARATOR, $cache_group );
		// we will seperate the dirs by 1000s
		$cache_group_explode = explode ( DIRECTORY_SEPARATOR, $cache_group );
		$cache_group_new = array ();
		foreach ( $cache_group_explode as $item ) {
			if (intval ( $item ) != 0) {
				$item_temp = intval ( $item ) / 1000;
				$item_temp = ceil ( $item_temp );
				$item_temp = $item_temp . '000';
				$cache_group_new [] = $item_temp;
				$cache_group_new [] = $item;
			} else {
				$cache_group_new [] = $item;
			}
		}
		$cache_group = implode ( DIRECTORY_SEPARATOR, $cache_group_new );
		if ($deleted_cache_dir == false) {
			$cacheDir = CACHEDIR . $cache_group;
		} else {
			// $cacheDir = CACHEDIR . 'deleted' . DIRECTORY_SEPARATOR . date (
			// 'YmdHis' ) . DIRECTORY_SEPARATOR . $cache_group;
			$cacheDir = CACHEDIR . $cache_group;
		}
		if (! is_dir ( $cacheDir )) {
			mkdir_recursive ( $cacheDir );
		}
		
		if (! defined ( $cache_content )) {
			define ( $cache_content, $cacheDir );
		}
		
		return $cacheDir;
	} else {
		if (! defined ( $cache_content )) {
			define ( $cache_content, $cache_group );
		}
		
		return $cache_group;
	}
}

/**
 *
 *
 *
 * Gets encoded data from the cache as a string.
 *
 *
 * @param string $cache_id
 *        	of the cache
 * @param string $cache_group
 *        	(default is 'global') - this is the subfolder in the cache dir.
 *        	
 * @return string
 * @author Peter Ivanov
 * @since Version 1.0
 */
function cache_get_content_encoded($cache_id, $cache_group = 'global', $time = false) {
	/* $function_cache_id = false;
	$args = func_get_args ();
	foreach ( $args as $k => $v ) {
		$function_cache_id = $function_cache_id . serialize ( $k ) . serialize ( $v );
	}
	$function_cache_id = __FUNCTION__ . crc32 ( $function_cache_id );
	$cache_content = 'CACHE_GET_CONTENT_' . $function_cache_id;
	
	if (! defined ( $cache_content )) {
	} else {
		
	//	return (constant ( $cache_content ));
	} */
	
	if ($cache_group === null) {
		
		$cache_group = 'global';
	}
	
	if ($cache_id === null) {
		
		return false;
	}
	
	$cache_id = trim ( $cache_id );
	
	$cache_group = $cache_group . DS;
	
	$cache_group = reduce_double_slashes ( $cache_group );
	
	$cache_file = cache_get_file_path ( $cache_id, $cache_group );
	$get_file = $cache_file;
	$cache = false;
	try {
		
		if ($cache_file != false) {
			
			if (isset ( $get_file ) == true and is_file ( $cache_file )) {
				
				// this is slower
				// $cache = implode('', file($cache_file));
				
				// this is faster
				$cache = file_get_contents ( $cache_file );
			}
		}
	} catch ( Exception $e ) {
		$cache = false;
	}
	
	if (isset ( $cache ) and strval ( $cache ) != '') {
		
		$search = CACHE_CONTENT_PREPEND;
		
		$replace = '';
		
		$count = 1;
		
		$cache = str_replace ( $search, $replace, $cache, $count );
	}
	
	if (($cache) != '') {
		/* 
		if (! defined ( $cache_content )) {
			if (strlen ( $cache_content ) < 50) {
				define ( $cache_content, $cache );
			}
		} */
		
		return $cache;
	}
/* 	if (! defined ( $cache_content )) {
	//	define ( $cache_content, false );
	} */
	return false;
}

/**
 *
 *
 *
 * Gets the data from the cache.
 *
 *
 * Unserilaizer for the saved data from the cache_get_content_encoded() function
 * 
 * @param string $cache_id
 *        	of the cache
 * @param string $cache_group
 *        	(default is 'global') - this is the subfolder in the cache dir.
 *        	
 * @return mixed
 * @author Peter Ivanov
 * @since Version 1.0
 * @uses cache_get_content_encoded
 */
function cache_get_content($cache_id, $cache_group = 'global', $time = false) {
	$cache = cache_get_content_encoded ( $cache_id, $cache_group, $time );
	
	if ($cache == '') {
		
		return false;
	} else {
		
		$cache = unserialize ( $cache );
		
		return $cache;
	}
}

/**
 *
 *
 * Stores your data in the cache.
 * It can store any value, such as strings, array, objects, etc.
 *
 * @param mixed $data_to_cache
 *        	your data
 * @param string $cache_id
 *        	of the cache, you must define it because you will use it later to
 *        	load the file.
 * @param string $cache_group
 *        	(default is 'global') - this is the subfolder in the cache dir.
 *        	
 * @return boolean
 * @author Peter Ivanov
 * @since Version 1.0
 * @uses cache_write_to_file
 */
function cache_save($data_to_cache, $cache_id, $cache_group = 'global') {
	return cache_store_data ( $data_to_cache, $cache_id, $cache_group );
}
function cache_store_data($data_to_cache, $cache_id, $cache_group = 'global') {
	if ($data_to_cache == false) {
		
		return false;
	} else {
		
		$data_to_cache = serialize ( $data_to_cache );
		
		// var_dump($data_to_cache);
		// $data_to_cache = base64_encode ( $data_to_cache );
		// .$data_to_cache = ($data_to_cache);
		
		cache_write_to_file ( $cache_id, $data_to_cache, $cache_group );
		
		return true;
	}
}
function cache_write($data_to_cache, $cache_id, $cache_group = 'global') {
	return cache_write_to_file ( $cache_id, $data_to_cache, $cache_group );
}

/**
 * Writes the cache file in the CACHEDIR directory.
 * 
 * @param string $cache_id
 *        	of the cache
 * @param string $content
 *        	content for the file, must be a string, if you want to store
 *        	object or array, please use the cache_store_data() function
 * @param string $cache_group
 *        	(default is 'global') - this is the subfolder in the cache dir.
 *        	
 * @return string
 * @author Peter Ivanov
 * @since Version 1.0
 * @see cache_store_data
 */
function cache_write_to_file($cache_id, $content, $cache_group = 'global') {
	if (strval ( trim ( $cache_id ) ) == '') {
		
		return false;
	}
	
	$cache_file = cache_get_file_path ( $cache_id, $cache_group );
	
	if (strval ( trim ( $content ) ) == '') {
		
		return false;
	} else {
		$cache_index = CACHEDIR . 'index.html';
		
		$cache_content1 = 'CACHE_INDEX_FILE_' . crc32 ( $cache_index );
		
		if (! defined ( $cache_content1 )) {
			
			if (is_file ( $cache_index ) == false) {
				
				@touch ( $cache_index );
			}
			if (! defined ( $cache_content1 )) {
				define ( $cache_content1, 1 );
			}
		}
		
		$see_if_dir_is_there = dirname ( $cache_file );
		
		$cache_content1 = 'CACHE_DIR_INDEX_' . crc32 ( $see_if_dir_is_there );
		
		if (! defined ( $cache_content1 )) {
			
			if (is_dir ( $see_if_dir_is_there ) == false) {
				
				mkdir_recursive ( $see_if_dir_is_there );
			}
			if (! defined ( $cache_content1 )) {
				define ( $cache_content1, 1 );
			}
		}
		
		$content = CACHE_CONTENT_PREPEND . $content;
		// var_dump ( $cache_file, $content );
		try {
			$cache = file_put_contents ( $cache_file, $content );
		} catch ( Exception $e ) {
			// $this -> cache_storage[$cache_id] = $content;
			$cache = false;
		}
	}
	
	return $content;
}