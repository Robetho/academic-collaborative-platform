
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audio/Video Call</title>
    <style>
        #local-video, #remote-video {
            width: 45%;
        }
    </style>
</head>
<body>
     <!-- <h2>Audio/Video Call</h2> -->
    
    <!-- <video id="local-video" autoplay muted></video>
    <video id="remote-video" autoplay></video> -->

    <div style="display: none;">
        <button id="start-call">Start Call</button>
        <button id="end-call" disabled>End Call</button>
   </div>

    <center><h2>Voice Recording</h2><br>
    <button id="start-recording">Start Recording</button>
    <button id="stop-recording" disabled>Stop Recording</button><br><br>
    <audio  id="audio-player" controls style="height: 10vh; width: 50%;"></audio><br>
        <button id="submit-audio" disabled>Submit Audio</button>
    </center>
    

    <center><h2>Video Recording</h2><br>
    <button id="start-video-recording">Start Video Recording</button>
    <button id="stop-video-recording" disabled>Stop Video Recording</button><br><br>
    <video  id="recorded-video" controls style="height: 20vh; width: 50%;"></video><br>
    <button id="submit-video" disabled>Submit Video</button></center><br>

    <script>
        // WebSocket setup for signaling (make sure you have a server to handle WebSocket)
        const socket = new WebSocket('ws://your-signaling-server-url'); // Replace with your WebSocket URL

        let localStream;
        let peerConnection;
        const localVideo = document.getElementById('local-video');
        const remoteVideo = document.getElementById('remote-video');
        const startCallButton = document.getElementById('start-call');
        const endCallButton = document.getElementById('end-call');

        const configuration = {
            iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
        };

        socket.onopen = () => {
            console.log("WebSocket connection established.");
        };

        socket.onmessage = async (message) => {
            const data = JSON.parse(message.data);

            if (data.offer) {
                // Handle incoming offer
                await handleOffer(data.offer);
            } else if (data.answer) {
                // Handle incoming answer
                await handleAnswer(data.answer);
            } else if (data.iceCandidate) {
                // Handle incoming ICE candidate
                await handleICECandidate(data.iceCandidate);
            }
        };

        startCallButton.addEventListener('click', async () => {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            localVideo.srcObject = localStream;

            peerConnection = new RTCPeerConnection(configuration);
            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

            peerConnection.ontrack = event => {
                remoteVideo.srcObject = event.streams[0];
            };

            peerConnection.onicecandidate = event => {
                if (event.candidate) {
                    socket.send(JSON.stringify({ iceCandidate: event.candidate }));
                }
            };

            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            socket.send(JSON.stringify({ offer: offer }));
        });

        endCallButton.addEventListener('click', () => {
            peerConnection.close();
            localStream.getTracks().forEach(track => track.stop());
            localVideo.srcObject = null;
            remoteVideo.srcObject = null;
            socket.send(JSON.stringify({ endCall: true }));
        });

        // Handle Offer
        async function handleOffer(offer) {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));

            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);

            socket.send(JSON.stringify({ answer: answer }));
        }

        // Handle Answer
        async function handleAnswer(answer) {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
        }

        // Handle ICE Candidate
        async function handleICECandidate(candidate) {
            await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        }

        // Audio Recording JavaScript (Same as your existing logic)
        let recorder;
        let audioBlob;
        const startRecordingButton = document.getElementById('start-recording');
        const stopRecordingButton = document.getElementById('stop-recording');
        const submitAudioButton = document.getElementById('submit-audio');
        const audioPlayer = document.getElementById('audio-player');

        startRecordingButton.addEventListener('click', async () => {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            recorder = new MediaRecorder(stream);
            recorder.start();
            stopRecordingButton.disabled = false;
            startRecordingButton.disabled = true;

            recorder.ondataavailable = event => {
                audioBlob = event.data;
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPlayer.src = audioUrl;
                submitAudioButton.disabled = false;
            };
        });

        stopRecordingButton.addEventListener('click', () => {
            recorder.stop();
            stopRecordingButton.disabled = true;
            startRecordingButton.disabled = false;
        });

        submitAudioButton.addEventListener('click', () => {
            const formData = new FormData();
            formData.append('audio', audioBlob, 'audio_recording.wav');

            fetch('save_audio.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                submitAudioButton.disabled = true;
            })
            .catch(error => console.error('Error:', error));
        });

        // Video Recording JavaScript (Same as your existing logic)
        let videoRecorder;
        let videoBlob;
        const startVideoRecordingButton = document.getElementById('start-video-recording');
        const stopVideoRecordingButton = document.getElementById('stop-video-recording');
        const submitVideoButton = document.getElementById('submit-video');
        const recordedVideo = document.getElementById('recorded-video');

        startVideoRecordingButton.addEventListener('click', async () => {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            videoRecorder = new MediaRecorder(stream);
            videoRecorder.start();
            stopVideoRecordingButton.disabled = false;
            startVideoRecordingButton.disabled = true;

            videoRecorder.ondataavailable = event => {
                videoBlob = event.data;
                const videoUrl = URL.createObjectURL(videoBlob);
                recordedVideo.src = videoUrl;
                submitVideoButton.disabled = false;
            };
        });

        stopVideoRecordingButton.addEventListener('click', () => {
            videoRecorder.stop();
            stopVideoRecordingButton.disabled = true;
            startVideoRecordingButton.disabled = false;
        });

        submitVideoButton.addEventListener('click', () => {
            const formData = new FormData();
            formData.append('video', videoBlob, 'video_recording.webm');

            fetch('save_video.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                submitVideoButton.disabled = true;
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>

