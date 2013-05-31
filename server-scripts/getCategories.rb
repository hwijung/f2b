require 'rubygems'
require 'json'
require 'rmetaweblog'
require 'mysql2'

# Command-line arguments
@username = ARGV[0]

# Create MySQL Database Connection
@db_con = Mysql2::Client.new( :host=>'localhost', :username=>'root', :password=>'!qazxsw2' )

# Use F2B database 
@db_con.query 'use f2b'

# Query Wordpress user information
results = @db_con.query( 'SELECT * FROM wp_account WHERE user = "' + @username + '"', :as=>'hash' )

# Set up blog account
results.each(:as => :hash) do |row| 
	@blog_id = row['wp_id']
	@blog_password = row['wp_password']
	@blog_url = row['wp_address']
	@blog_hostname = row['wp_hostname']
	@blog_apipath = row['wp_apipath']
end

# Create Blog Object
@blog = RMetaWebLog.new(@blog_hostname, @blog_apipath, {
	:blog_url => @blog_url,
	:blog_id => "1111",
	:api_user => @blog_id,
 	:api_pass => @blog_password } )

puts @blog.categories.to_json
