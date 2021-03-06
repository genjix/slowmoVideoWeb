{{Title:Download slowmoVideo %s}}
{{H1:Download}}

{{Bind:canonical=<link rel="canonical" href="http://slowmoVideo.granjow.net/download.html"/>}}

{{Bind:php=<?php

date_default_timezone_set('UTC');

function newest($suffix = '', $filter = '', $dir = 'builds')
{
    $fileList = array();
    $files = scandir($dir);
    if (!$files) {
        echo '<p>No files in <em>' . $dir . '</em>.</p>';
        return;
    }
    foreach ($files as $entry) {

        $f = $dir . '/' . $entry;
        
        $fileSizeBits = filesize('builds/' . $entry);
        if ($fileSizeBits/1024 < 1000) {
            $fileSize = sprintf('%.0f KB', $fileSizeBits/1024);
        } else if ($fileSizeBits/1024/1024 < 10) {
            $fileSize = sprintf('%.1f MB', $fileSizeBits/1024/1024);
        } else {
            $fileSize = sprintf('%.0f MB', $fileSizeBits/1024/1024);
        }

        $fileInfo = array(
            '%name%' => $entry,
            '%path%' => $f,
            '%date%' => date('Y-m-d H:i:s', filectime($f)),
            '%size%' => $fileSize,
            '%vers%' => '',
        );
        
        
        $matches = array();
        $n = preg_match('/slowmoVideo_(\d.\d.\d(?:-\d)?)/', $entry, $matches);
        if ($n > 0) {
            $fileInfo['%vers%'] = $matches[1];
        }

        if (is_file($f)) {
            if ( strlen($suffix) == 0 || substr_compare($f, $suffix, -strlen($suffix)) == 0 ) {
                if ( strlen($filter) == 0 || strpos($f, $filter) !== false ) {
                    $fileList[filectime($f) . md5($f)] = $fileInfo;
                }
            }
        }
    }
    
    krsort($fileList);
    
    if (count($fileList) > 0) {
        return reset($fileList);
    } else {
        return null;
    }
}

function downloadButton($suffix = '', $filter = '', $text, $cssClass, $dir = 'builds')
{
    $first = newest($suffix, $filter, $dir);
    if ($first == null) {
        return "";
    }
    
    //print_r($first);
    
    $first['%text%'] = $text;
    $first['%css%'] = $cssClass;
    return strtr("<a class='download %css%' href='%path%'>%vers% %text% (%size%)</a>", $first);
}

?>
}}

'''For Windows''' you additionally need to download ffmpeg from [http://ffmpeg.zeranoe.com/builds/ Zeranoe] ''(32-bit Builds (Static))'' and extract $$ffmpeg.exe$$ into the same directory as $$slowmoUI.exe$$. '''Please upgrade to 0.3.1''' as older versions do not work anymore with current ffmpeg versions.

'''On Linux,''' if slowmoVideo crashes in a file dialog with an error message like $$QSpiAccessible::accessibleEvent not handled$$, removing $$qt-at-spi$$ should fix the problem.

<?php newest('', 'win32'); ?>
{|
! Windows
| <?php echo downloadButton('7z', 'win32', '.7z', 'windows'); ?> <?php echo downloadButton('zip', 'win32', '.zip', 'windows'); ?> <?php echo downloadButton('exe', 'win32', '.exe', 'windows'); ?>
|-
! Ubuntu
| <a href="https://launchpad.net/~brousselle/+archive/slowmovideo" class="download linux">PPA on Launchpad</a>
|-
! Sources
| <?php echo downloadButton('bz2', 'sources', '.bz2', 'sources'); ?> <a href="https://github.com/slowmoVideo" class="download source">GitHub</a> <a href="git://github.com/slowmoVideo/slowmoVideo.git" class="download source">git</a>
|-
! Older systems
| <?php echo downloadButton('deb', 'ubuntu11.10_i386', 'i386.deb (11.10)', 'linux'); ?> <?php echo downloadButton('deb', 'ubuntu10.04_i386', 'i386.deb (10.04 LTS)', 'linux'); ?> <?php echo downloadButton('deb', 'ubuntu11.10_amd64', 'amd64.deb (11.10)', 'linux'); ?> <?php echo downloadButton('deb', 'ubuntu10.04_amd64', 'amd64.deb (10.04 LTS)', 'linux'); ?>
|}

Older packages can be found [[builds.php here]].

=== Mac OS X ===
There is no native port yet for OS X, but Wine or Crossover allow to run the Windows build:
# Download slowmoVideo for Windows and unzip it
# Download the ffmpeg Win32 static builds from [http://ffmpeg.zeranoe.com/builds/ Zeranoe] and place ffmpeg.exe in the same directory as $$slowmoUI.exe$$
# Install [http://winebottler.kronenberg.org/ Winebottler] or [http://www.codeweavers.com/products/crossover-mac/ Crossover] ($)
# Open slowmoVideo with Wine from Winebottler or with Crossover
Don’t forget to use control (Ctrl) instead of command (Cmd) for Undo and other operations.


=== Requirements ===
In a nutshell:
* Linux or Windows
* Any GPU for better results

slowmoVideo runs on Linux and on Windows. It comes with two algorithms for calculating the Optical Flow, a CPU based one from OpenCV and a GPU based one from GPU-KLT+FLOW.

The GPU flow program (optional) is called $$flowBuilder$$ and usually produces evidently better results. Is not yet available for Windows.

slowmoVideo is not yet available on OS X since the author does not have a Mac. It should compile easily however. [[contribute.html Help is appreciated]] :)


== Compiling ==
''The following instructions are only important for you if you want to develop slowmoVideo or if there is no package for your distribution.''

First you may have to resolve some dependencies. CMake will most likely inform you about missing packages as well, just in case some are missing here.
* slowmoVideo requires ffmpeg or libav, the Qt4 libraries, and OpenCV.
* Additionally for compiling you need cmake, g++, and git.




==== Installing the required packages … ====
Some distribution specific installation instructions:

{{:tplSec.txt|
==== Debian/Ubuntu ====
Note: On debian, the OpenCV package is called ''lib''opencv-dev, on Ubuntu it is just opencv-dev.
$$
apt-get install build-essential cmake git ffmpeg libavformat-dev libavcodec-dev libswscale-dev libqt4-dev freeglut3-dev libglew1.5-dev libsdl1.2-dev libjpeg-dev libopencv-video-dev libopencv-highgui-dev opencv-dev
$$

For Ubuntu 14.04 follow these build instructions:

$$
$ sudo apt-get install build-essential cmake git libavformat-dev libavcodec-dev libswscale-dev libqt4-dev freeglut3-dev libglew1.5-dev libsdl1.2-dev libjpeg-dev libopencv-video-dev libopencv-highgui-dev libopencv-dev
$ sudo add-apt-repository ppa:jon-severinsson/ffmpeg
$ sudo apt-get update
$ sudo apt-get install ffmpeg
$ git clone git://github.com/slowmoVideo/slowmoVideo.git
$ cd slowmoVideo/
$ mkdir build
$ cd build/
$ cmake -DENABLE_TESTS=FALSE ../src 
$ make
$ sudo make install
$$ 

}}

{{:tplSec.txt|
==== Fedora ====
For ffmpeg you may need to add the rpmfusion repository first, as explained [http://rpmfusion.org/Configuration/ here].

$$
yum install cmake ffmpeg ffmpeg-devel git qt4-devel gcc-c++ glew-devel glut-devel SDL-devel libpng-devel libjpeg-devel opencv-devel
$$
}}

{{:tplSec.txt|
==== openSUSE ====
As with Fedora an additional repository is required for ffmpeg, [http://en.opensuse.org/Additional_package_repositories#Packman Packman].

$$
zypper in cmake ffmpeg libffmpeg-devel git libqt4-devel gcc-c++ glew-devel freeglut-devel libSDL-devel opencv-devel
$$
}}


==== … and compiling slowmoVideo ====
You can get the source code by either downloading the .bz2 archive, or by using git — latter is the recommended way as you can easily update the sources. Git works as follows:
$$((title=Source code checkout))
$ git clone git://github.com/slowmoVideo/slowmoVideo.git
$ cd slowmoVideo
$ git pull # ''Run this whenever you want to update to the latest source code version''
$$
Now slomoVideo needs to be compiled and installed. (Note: This has changed recently, and flowBuilder does not need to be compiled separately anymore.)
$$((title=Compiling))
$ mkdir build 
$ cd build
$ cmake ../src
$ make -j3
$ sudo make install
$$
If you want to install slowmoVideo to a different directory (e.g. your home directory) so you don’t need root rights, run cmake as below instead. By default, slowmoVideo is installed to $$/usr/local/bin/$$.
$$((title=Different install directory))
$ cmake ../src -DCMAKE_INSTALL_PREFIX=''/home/archibald/slowmoVideo''
$$

If all of this worked, you should be able to run $$slowmoUI$$. If not, please find help in the [https://plus.google.com/u/0/communities/116570263544012246711 Google+ community].
