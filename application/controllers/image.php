<?php

class Image extends Controller {
        function __construct() {
                parent::Controller();
                $this->load->library('pagination');
        }

        function index($fileName) {
                $image = $this->chan->retrieveUploads(array('fileName' => $fileName));

                if(!$image) show_error('Nothing to see here, there\'s not an image with that name!', 404);

                if(!empty($image->uploadPostID)) {
                        $data['original_post'] = "http://thecolorless.net/thread/" . $image->uploadThreadID . "/post/" . $image->uploadPostID;
                }
                $data['original_source'] = 'http://thecolorless.net/uploads/' . (strstr($image->uploadFilename, '.') ? $fileName : $image->uploadFilename . '_original.' . substr(strrchr($image->picture_file_name,'.'),1));
		$data['medium_source'] = str_replace("_original", "_medium", $data['original_source']);
                $data['tags'] = $this->chan->getUploadTags($image->uploadID);

                $data['id'] = $image->uploadID;
                $data['likes'] = $this->chan->imageLikeCount($image->uploadID);
                $data['userID'] = $image->uploadUserID;
                $data['timestamp'] = $image->uploadCreatedAt;
                $data['user'] = $this->user->getUsers(array('userID'=>$data['userID']));
                $data['filename'] = $fileName;
                $data['dimensions'] = $image->uploadWidth . 'x' . $image->uploadHeight;
                $data['size'] = number_format($image->picture_file_size / 1024, 2) . ' kB';
                $data['source'] = $image->uploadSource;

                $this->load->view('image', $data);
        }


        function delete($name) {
                $image = $this->chan->retrieveUploads(array('fileName' => $name));

                if ($this->user->isMod() || $this->user->isAdmin() || $this->session->userdata('userID') == $image->uploadUserID) {
                        $this->chan->deleteUpload($name);
                }
                redirect('/i/');
        }

}
?>
