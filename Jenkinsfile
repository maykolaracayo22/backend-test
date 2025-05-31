pipeline {
    agent {
        docker {
            image 'composer:2'                  // Usa la imagen oficial con PHP y Composer
            args '-v $PWD:/app'                // Monta el workspace en /app dentro del contenedor
        }
    }

    environment {
        SONARQUBE = 'sonarqube'               // Nombre configurado en Jenkins para SonarQube
    }

    stages {
        stage('Clone') {
            steps {
                timeout(time: 2, unit: 'MINUTES') {
                    git branch: 'main',
                        credentialsId: 'githubtoken1',      // Cambia por el ID real de tu credencial GitHub en Jenkins
                        url: 'https://github.com/maykolaracayo22/backend-test.git'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                echo 'Saltando instalación de dependencias porque vendor/ está en el repo'
            }
        }

        stage('Run Tests') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    sh 'cd reservasback && ./vendor/bin/phpunit --configuration phpunit.xml'
                }
            }
        }

        stage('SonarQube Analysis') {
            environment {
                scannerHome = tool 'SonarQubeScanner'
            }
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    withSonarQubeEnv(SONARQUBE) {
                        sh """
                        cd reservasback && \\
                        ${scannerHome}/bin/sonar-scanner \\
                        -Dsonar.projectKey=backend-test \\
                        -Dsonar.sources=app,routes,database \\
                        -Dsonar.php.coverage.reportPaths=storage/logs/clover.xml \\
                        -Dsonar.host.url=http://tu_sonarqube_url:9000
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Aquí puedes agregar los comandos para desplegar tu aplicación Laravel.'
            }
        }
    }
}
