(function() {
    tinymce.create('tinymce.plugins.S3bubble', {
        init : function(ed, url) {

            /*
             * S3bubble audio playlist
             */
            ed.addButton('s3bubble_waveform_playlist_shortcode', {
                title : 'S3Bubble Audio Waveform Shortcode',
                cmd : 's3bubble_waveform_playlist_shortcode',
                image : url + '/s3bubbletiny_audio_playlist.png'
            });
            ed.addCommand('s3bubble_waveform_playlist_shortcode', function() {
                tb_show('S3Bubble Audio Waveform Shortcode', 'admin-ajax.php?action=s3bubble_waveform_playlist_ajax');
            });
            
            /*
             * S3bubble audio playlist
             */
            ed.addButton('s3bubble_live_stream_mobile_shortcode', {
                title : 'S3Bubble Main Live Steam Shortcode',
                cmd : 's3bubble_live_stream_mobile_shortcode',
                image : url + '/s3bubbletiny_live_mobile_stream.png'
            });
            ed.addCommand('s3bubble_live_stream_mobile_shortcode', function() {
                tb_show('S3Bubble Main Live Steam Shortcode', 'admin-ajax.php?action=s3bubble_live_stream_mobile_ajax');
            });

            /*
             * S3bubble audio playlist
             */
            ed.addButton('s3bubble_live_stream_shortcode', {
                title : 'Generate S3Bubble Live Steam Shortcode',
                cmd : 's3bubble_live_stream_shortcode',
                image : url + '/s3bubbletiny_live_stream.png'
            });
            ed.addCommand('s3bubble_live_stream_shortcode', function() {
                tb_show('Generate S3Bubble Live Stream', 'admin-ajax.php?action=s3bubble_live_stream_ajax');
            });

            /*
             * S3bubble audio playlist
             */
            ed.addButton('s3bubble_audio_playlist_shortcode', {
                title : 'Generate S3Bubble Audio Playlist Shortcode',
                cmd : 's3bubble_audio_playlist_shortcode',
                image : url + '/s3bubbletiny_audio_playlist.png'
            });
            ed.addCommand('s3bubble_audio_playlist_shortcode', function() {
            	tb_show('Generate S3Bubble Audio Playlist Shortcode', 'admin-ajax.php?action=s3bubble_audio_playlist_ajax');
            });
            
            /*
             * S3bubble video playlist
             */
            ed.addButton('s3bubble_video_playlist_shortcode', {
                title : 'Generate S3Bubble Video Playlist Shortcode',
                cmd : 's3bubble_video_playlist_shortcode',
                image : url + '/s3bubbletiny_video_playlist.png'
            });
            ed.addCommand('s3bubble_video_playlist_shortcode', function() {
            	tb_show('Generate S3Bubble Video Playlist Shortcode', 'admin-ajax.php?action=s3bubble_video_playlist_ajax');
            });
            
            /*
             * S3bubble audio single
             */
            ed.addButton('s3bubble_audio_single_shortcode', {
                title : 'Generate S3Bubble Single Audio Shortcode',
                cmd : 's3bubble_audio_single_shortcode',
                image : url + '/s3bubbletiny_audio_single.png'
            });
            ed.addCommand('s3bubble_audio_single_shortcode', function() {
            	tb_show('Generate S3Bubble Single Audio Shortcode', 'admin-ajax.php?action=s3bubble_audio_single_ajax');
            });
            
            /*
             * S3bubble video
             */
            ed.addButton('s3bubble_video_single_shortcode', {
                title : 'Generate S3Bubble Single Video Shortcode',
                cmd : 's3bubble_video_single_shortcode',
                image : url + '/s3bubbletiny_video_single.png'
            });
            ed.addCommand('s3bubble_video_single_shortcode', function() {
            	tb_show('Generate S3Bubble Single Video Shortcode', 'admin-ajax.php?action=s3bubble_video_single_ajax');
            });
            
        },
    });
    // Register plugin
    tinymce.PluginManager.add( 's3bubble', tinymce.plugins.S3bubble );
})();