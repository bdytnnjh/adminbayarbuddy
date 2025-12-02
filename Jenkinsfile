pipeline {
    agent any

    environment {
        // Docker Registry Configuration
        DOCKER_REGISTRY = 'registry.sendistudio.id:5000'
        IMAGE_NAME = 'projects/admin-bayarbuddy'
        IMAGE_TAG = "${BUILD_NUMBER}"
        FULL_IMAGE_NAME = "${DOCKER_REGISTRY}/${IMAGE_NAME}:${IMAGE_TAG}"
        LATEST_IMAGE_NAME = "${DOCKER_REGISTRY}/${IMAGE_NAME}:latest"

        // Docker Registry Credentials (configure in Jenkins Credentials)
        DOCKER_REGISTRY_CREDS = credentials('docker-registry-credentials')
    }

    stages {
        stage('Checkout') {
            steps {
                cleanWs()
                deleteDir()
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Environment Setup') {
            steps {
                echo 'Setting up build environment...'
                script {
                    // Print build information
                    echo "Building ${IMAGE_NAME}:${IMAGE_TAG}"
                    echo "Registry: ${DOCKER_REGISTRY}"
                    echo "Build Number: ${BUILD_NUMBER}"
                    echo "Git Commit: ${env.GIT_COMMIT}"
                    echo "Git Branch: ${env.GIT_BRANCH}"
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                echo 'Building Docker image...'
                script {
                    try {
                        // Build the Docker image
                        def dockerImage = docker.build("${FULL_IMAGE_NAME}", '.')

                        // Tag as latest
                        sh "docker tag ${FULL_IMAGE_NAME} ${LATEST_IMAGE_NAME}"

                        echo "Successfully built Docker image: ${FULL_IMAGE_NAME}"
                    } catch (Exception e) {
                        echo "Docker build failed: ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Test Docker Image') {
            steps {
                echo 'Testing Docker image...'
                script {
                    try {
                        // Test if the image can start successfully
                        sh """
                            echo 'Testing container startup...'
                            docker run --rm -d --name test-${BUILD_NUMBER} -p 0:3100 ${FULL_IMAGE_NAME}
                            sleep 10

                            # Check if container is running
                            if docker ps | grep test-${BUILD_NUMBER}; then
                                echo 'Container started successfully'
                                docker stop test-${BUILD_NUMBER}
                            else
                                echo 'Container failed to start'
                                exit 1
                            fi
                        """
                    } catch (Exception e) {
                        echo "Docker image test failed: ${e.getMessage()}"
                        // Clean up test container if it exists
                        sh "docker rm -f test-${BUILD_NUMBER} || true"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Push to Registry') {
            steps {
                echo 'Pushing image to Docker registry...'
                script {
                    try {
                        // Login to Docker registry
                        docker.withRegistry("https://${DOCKER_REGISTRY}", 'docker-registry-credentials') {
                            // Push versioned image
                            sh "docker push ${FULL_IMAGE_NAME}"
                            echo "Successfully pushed: ${FULL_IMAGE_NAME}"

                            // Push latest tag
                            sh "docker push ${LATEST_IMAGE_NAME}"
                            echo "Successfully pushed: ${LATEST_IMAGE_NAME}"
                        }
                    } catch (Exception e) {
                        echo "Failed to push to registry: ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Trigger Redeploy in Portainer') {
            steps {
                echo 'Sending webhook to Portainer to trigger redeployment...'
                withCredentials([string(credentialsId: 'webhook-freelance-admin-bayar-buddy', variable: 'WEBHOOK_URL')]) {
                    sh 'curl -k -X POST ${WEBHOOK_URL}'
                }
            }
        }

        stage('Cleanup Local Images') {
            steps {
                echo 'Cleaning up local Docker images...'
                script {
                    try {
                        // Remove local images to save disk space
                        sh """
                            docker rmi ${FULL_IMAGE_NAME} || true
                            docker rmi ${LATEST_IMAGE_NAME} || true

                            # Clean up dangling images
                            docker image prune -f || true
                        """
                        echo 'Local cleanup completed'
                    } catch (Exception e) {
                        echo "Cleanup warning: ${e.getMessage()}"
                    // Don't fail the build for cleanup issues
                    }
                }
            }
        }
    }

    post {
        always {
            echo 'Pipeline execution completed'
            script {
                // Clean up any remaining test containers
                sh "docker rm -f test-${BUILD_NUMBER} || true"
            }
        }

        success {
            echo '✅ Pipeline completed successfully!'
            script {
                echo "🚀 Image deployed: ${FULL_IMAGE_NAME}"
                echo "🏷️  Latest tag: ${LATEST_IMAGE_NAME}"
                echo "📊 Build #${BUILD_NUMBER} completed successfully"
            }
        }

        failure {
            echo '❌ Pipeline failed!'
            script {
                echo "💥 Build #${BUILD_NUMBER} failed"
                echo "🔍 Check the logs above for error details"

                // Clean up any remaining containers/images on failure
                sh '''
                    docker rm -f test-${BUILD_NUMBER} || true
                    docker rmi ${FULL_IMAGE_NAME} || true
                    docker rmi ${LATEST_IMAGE_NAME} || true
                '''
            }
        }

        unstable {
            echo '⚠️ Pipeline completed with warnings'
        }
    }
}
