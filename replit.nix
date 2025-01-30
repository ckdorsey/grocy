
{ pkgs }: {
  deps = [
    pkgs.php82
    pkgs.php82Packages.composer
    pkgs.php82Extensions.pdo
    pkgs.php82Extensions.sqlite3
    pkgs.php82Extensions.curl
    pkgs.php82Extensions.openssl
    pkgs.php82Extensions.mbstring
    pkgs.php82Extensions.intl
    pkgs.unzip
  ];
}
