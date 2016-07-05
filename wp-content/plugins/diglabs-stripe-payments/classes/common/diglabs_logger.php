<?php
require_once 'stoyanov_logger.php';

if(!class_exists('DigLabs_Logger'))
{
	class DigLabs_Logger
	{
		const DEBUG 	= 1;	// Most Verbose
		const INFO 		= 2;	// ...
		const WARN 		= 3;	// ...
		const ERROR 	= 4;	// ...
		const FATAL 	= 5;	// Least Verbose
		const OFF 		= 6;	// Nothing at all.

        private $me;
		private $log_folder;
		private $log_file;
		private $log;
        private $is_debug;

		public function __construct($log_folder, $level = DigLabs_Logger::INFO, $is_debug = false)
		{
            $this->me = dirname( __FILE__ );
			$file_name = date('Y_m_d') . '_log.txt';
			$log_folder = rtrim( $log_folder, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
			$file_path = $log_folder . $file_name;

			$this->log_folder = $log_folder;
			$this->log_file = $file_path;

            $this->is_debug = $is_debug;


            $this->log = new Stoyanov_Logger($file_path, $level);
		}

		public function __destruct()
		{
			$this->log->Save();
		}

        public function htaccess()
        {
            try
            {
                $htaccess = $this->log_folder . ".htaccess";
                if(!file_exists($htaccess))
                {
                    $content = "deny from all";
                    if ( $file_handle = @fopen( $htaccess, 'w' ) )
                    {
                        fwrite( $file_handle, $content );
                        fclose( $file_handle );
                    }
                }
                if(file_exists($htaccess))
                {
                    return true;
                }
            }
            catch(Exception $e)
            {
                return false;
            }

            return false;
        }

		public function is_active()
		{
            // Only log if the htaccess is in place.
            //
            if(!$this->htaccess())
            {
                return false;
            }

            // Only log if we are debugging.
            //
            if(!$this->is_debug)
            {
                return false;
            }

            try
            {
                if( !file_exists( $this->log_folder ))
                {
                    if( !wp_mkdir_p( $this->log_folder ) )
                    {
                        return false;
                    }
                }
                if( !file_exists($this->log_file))
                {
                    if( $file_handle = @fopen( $this->log_file, 'w' ) )
                    {
                        fwrite( $file_handle, '' );
                        fclose( $file_handle );
                    }
                    else
                    {
                        return false;
                    }
                }
                if(!is_writable($this->log_file))
                {
                    return false;
                }
            }
            catch(Exception $e)
            {
                // Must not have access to this file.
                //
                return false;
            }

			return true;
		}

		public function info($msg)
		{
			if($this->is_active())
			{
				$this->log->LogInfo($msg);
			}
		}

		public function debug($msg)
		{
			if($this->is_active())
			{
				$this->log->LogDebug($msg);
			}
		}

		public function warning($msg)
		{
			if($this->is_active())
			{
				$this->log->LogWarn($msg);
			}
		}

		public function error($msg)
		{
			if($this->is_active())
			{
				$this->log->LogError($msg);
			}
		}

		public function fatal($msg)
		{
			if($this->is_active())
			{
				$this->log->LogFatal($msg);
			}
		}
	}
}